<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTermRequest;
use App\Http\Requests\UpdateTermRequest;
use App\Models\Definition;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder as ElBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $termq = $request->query("term");
        $langq = $request->query("lang");

        $terms = Term::query()
            ->withCount("definitions")
            ->where("owner_id", $request->user()->id)
            ->when($termq, function (ElBuilder $query) use ($termq) {
                // TODO: Add def examples to vector.
                $vec =
                    "setweight(to_tsvector('en', terms.name), 'A')" .
                    " || setweight(to_tsvector('en', definitions.text), 'B')" .
                    " || setweight(to_tsvector('en', coalesce(definitions.comment, '')), 'C')";

                $query
                    ->join("definitions", "definitions.term_id", "=", "terms.id")
                    ->whereRaw("($vec) @@ plainto_tsquery('en', ?)", [$termq])
                    ->orWhere(
                        DB::raw(
                            "terms.name || definitions.text || coalesce(definitions.comment, '')"
                        ),
                        "ilike",
                        "%$termq%"
                    )
                    ->selectRaw(
                        // 32 implies a normalized ranking in interval [0, 1].
                        // For search terms that don't match any tsvector but match via ilike, set the rank to 0.5.
                        "coalesce(nullif(ts_rank_cd(($vec), plainto_tsquery('en', ?), 32), 0), 0.5) as _rank",
                        [$termq]
                    )
                    ->orderByRaw("_rank desc");
            })
            ->when($langq, function (ElBuilder $query) use ($langq) {
                $query->where("lang_id", $langq);
            })
            ->distinct()
            ->latest("updated_at")
            ->ddRawSql();
        // ->paginate()
        // ->appends($request->query());

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
