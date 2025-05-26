<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function edit(Request $request)
    {
        // Ideally, these queries should probably run in a transaction?

        $termsCount = Term::query()
            ->where("terms.owner_id", $request->user()->id)
            ->count();

        if ($termsCount < config("app.min_terms_count_for_quiz")) {
            return view("quiz", ["questions" => [], "termsCount" => $termsCount]);
        }

        $terms = Term::query()
            ->where("terms.owner_id", $request->user()->id)
            ->with([
                "definitions" => fn($query) => $query->inRandomOrder()->limit(1),
            ])
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $placeholders = $terms->map(fn() => "(?::bigint, ?::text)")->join(",");

        $wrongOptions = DB::query()
            ->fromRaw(
                "(values $placeholders) as ref_terms(id, definition)",
                $terms->flatMap(fn($t) => [$t->id, $t->definitions[0]->text])
            )
            ->joinLateral(
                Definition::query()
                    ->whereColumn("definitions.term_id", "<>", "ref_terms.id")
                    ->whereColumn("definitions.text", "<>", "ref_terms.definition")
                    ->whereRelation("term", "terms.owner_id", $request->user()->id)
                    ->select(["definitions.text", DB::raw("random() as _order")])
                    ->distinct()
                    ->orderBy("_order")
                    ->limit(2),
                "other"
            )
            ->select([DB::raw("ref_terms.id as ref_term_id"), "other.text"])
            ->get()
            ->groupBy("ref_term_id");

        $questions = $terms->map(function ($term) use ($wrongOptions) {
            $answer = $term->definitions[0]->text;
            $options = Arr::shuffle([
                $answer,
                ...Arr::pluck($wrongOptions[$term->id], "text"),
            ]);
            return [
                "term" => $term->name,
                "lang" => $term->lang->name,
                "options" => $options,
                "answerIndex" => array_search($answer, $options),
            ];
        });

        return view("quiz", ["questions" => $questions]);
    }
}
