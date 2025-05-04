<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTermRequest;
use App\Http\Requests\UpdateTermRequest;
use App\Models\Definition;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as ElBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $termq = $request->query("term");
        $langq = $request->query("lang");

        $termsQuery = Term::query()
            ->withCount("definitions")
            ->where("terms.owner_id", $request->user()->id)
            ->when($termq, function (ElBuilder $query) use ($termq) {
                $subquery = DB::table("definitions")
                    ->leftJoin(
                        DB::raw(
                            "lateral jsonb_array_elements_text(definitions.examples) as example"
                        ),
                        DB::raw(1),
                        DB::raw(1)
                    )
                    ->groupBy("definitions.term_id")
                    ->selectRaw(
                        "definitions.term_id" .
                            ", string_agg(definitions.text, ' ') as agg_text" .
                            ", coalesce(string_agg(definitions.comment, ' '), '') as agg_comment" .
                            ", coalesce(string_agg(example, ' '), '') as agg_example"
                    );

                $vec =
                    "setweight(to_tsvector('en', terms.name), 'A')" .
                    " || setweight(to_tsvector('en', d.agg_text), 'B')" .
                    " || setweight(to_tsvector('en', d.agg_example), 'C')" .
                    " || setweight(to_tsvector('en', d.agg_comment), 'C')";

                $query
                    ->joinSub($subquery, "d", "d.term_id", "terms.id")
                    ->where(function (ElBuilder $q) use ($termq, $vec) {
                        $q->where("terms.name", "ilike", "%$termq%")
                            ->orWhere("d.agg_text", "ilike", "%$termq%")
                            ->orWhere("d.agg_comment", "ilike", "%$termq%")
                            ->orWhere("d.agg_example", "ilike", "%$termq%")
                            ->orWhereRaw("($vec) @@ plainto_tsquery('en', ?)", [$termq]);
                    })
                    ->selectRaw(
                        // 32 implies a normalized ranking in interval [0, 1].
                        "greatest(" .
                            "ts_rank_cd(($vec), plainto_tsquery('en', ?), 32)" .
                            ", case when terms.name ilike ? then 0.9" .
                            "  when d.agg_text ilike ? then 0.5" .
                            "  when d.agg_comment ilike ? or d.agg_example ilike ? then 0.1" .
                            " end) as _rank",
                        [$termq, "%$termq%", "%$termq%", "%$termq%", "%$termq%"]
                    )
                    ->orderByRaw("_rank desc");
            })
            ->when($langq, function (ElBuilder $query) use ($langq) {
                $query->where("terms.lang_id", $langq);
            })
            ->latest("terms.updated_at");

        $terms = $termsQuery->paginate()->appends($request->query());
        $langs = Lang::query()->orderBy("name", "asc")->get();
        $allTermsCount = Term::query()
            ->where("owner_id", $request->user()->id)
            ->count();

        return view("terms.index", [
            "terms" => $terms,
            "langs" => $langs,
            "allTermsCount" => $allTermsCount,
        ]);
    }

    public function create()
    {
        $langs = Lang::query()->orderBy("name", "asc")->get();
        $emptyDef = ["text" => "", "examples" => [], "comment" => ""];

        return view("terms.create", [
            "langs" => $langs,
            "emptyDef" => $emptyDef,
        ]);
    }

    public function store(StoreTermRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $term = new Term();
            $term->name = $validated["term"];
            $term->lang_id = $validated["lang"];
            $term->owner_id = $request->user()->id;
            $term->save();

            $term->definitions()->createMany(
                Arr::map($validated["defs"], function ($rawDef) {
                    return [
                        "text" => $rawDef["text"],
                        "comment" => Arr::get($rawDef, "comment"),
                        "examples" => ($rawDef["examples"] ??= []),
                    ];
                })
            );
        });

        return redirect(route("terms.index"));
    }

    public function show(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions");
        return view("terms.show", ["term" => $term]);
    }

    public function edit(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions");
        $langs = Lang::query()->orderBy("name", "asc")->get();

        $emptyDef = [
            "id" => "",
            "text" => "",
            "examples" => [],
            "comment" => "",
        ];

        $defs = $term->definitions->map(
            fn(Definition $def) => Arr::only($def->toArray(), array_keys($emptyDef))
        );

        return view("terms.edit", [
            "term" => $term,
            "langs" => $langs,
            "emptyDef" => $emptyDef,
            "defs" => $defs,
        ]);
    }

    public function update(UpdateTermRequest $request, Term $term)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $term) {
            $defsToKeep = collect();

            foreach ($validated["defs"] as $rawDef) {
                $def = $term->definitions()->updateOrCreate(
                    ["id" => Arr::get($rawDef, "id")],
                    [
                        "text" => $rawDef["text"],
                        "comment" => Arr::get($rawDef, "comment"),
                        "examples" => ($rawDef["examples"] ??= []),
                    ]
                );

                $defsToKeep->push($def);
            }

            $deleteCount = $term
                ->definitions()
                ->getQuery()
                ->whereNotIn("id", $defsToKeep->map(fn($def) => $def->id))
                ->delete();

            // If just a def is updated/created/deleted, let the time reflect on the term.
            // (Laravel's save() method won't update the DB if the model isn't dirty.)
            if (
                $deleteCount ||
                $defsToKeep->some(fn(Definition $def) => $def->wasChanged())
            ) {
                $term->touch();
            }

            $term->name = $validated["term"];
            $term->lang_id = $validated["lang"];

            $term->save();
        });

        return redirect(route("terms.show", ["term" => $term]));
    }

    public function destroy(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->delete();

        return redirect(route("terms.index"))->with("status", "term-deleted");
    }
}
