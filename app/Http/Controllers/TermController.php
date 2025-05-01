<?php

namespace App\Http\Controllers;

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
                $vec =
                    "setweight(to_tsvector('en', terms.name), 'A')" .
                    " || setweight(to_tsvector('en', definitions.text), 'B')";

                $query
                    ->join("definitions", "definitions.term_id", "=", "terms.id")
                    ->whereRaw("($vec) @@ plainto_tsquery('en', ?)", [$termq])
                    ->selectRaw("ts_rank_cd(($vec), plainto_tsquery('en', ?)) as _rank", [
                        $termq,
                    ])
                    ->orderByRaw("_rank desc");
            })
            ->when($langq, function (ElBuilder $query) use ($langq) {
                $query->where("lang_id", $langq);
            })
            ->distinct()
            ->latest("updated_at")
            ->paginate()
            ->appends($request->query());

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

    public function store(Request $request)
    {
        $validated = $this->validator($request->input())->validate();

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

    public function update(Request $request, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $validated = $this->validator($request->input(), $term)->validate();

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

    // TODO: Consider using a FormRequest instead of this.
    protected function validator(array $input, Term $term = null)
    {
        $uniqueTermRule = Rule::unique("terms", "name")->where(
            fn(Builder $query) => $query
                ->where("owner_id", request()->user()->id)
                ->where("lang_id", request()->input("lang"))
        );

        $existingDefRule = Rule::exists("definitions", "id")->where(
            fn(Builder $query) => $query->where("term_id", $term->id)
        );

        $rules = [
            "term" => [
                "required",
                "max:255",
                $term ? $uniqueTermRule->ignoreModel($term) : $uniqueTermRule,
            ],
            "lang" => ["required", "exists:langs,id"],
            "defs" => ["required", "array", "min:1"],
            ...$term ? ["defs.*.id" => ["nullable", "integer", $existingDefRule]] : [],
            "defs.*.text" => ["required", "max:255"],
            "defs.*.examples" => ["array", "max:3"],
            "defs.*.examples.*" => ["required", "max:255"],
            "defs.*.comment" => ["max:255"],
        ];

        $messages = [
            "term.unique" => "You already have this :attribute.",
            // This rule should only fail if the page's DOM has been tampered with (or something similar)
            "defs.*.id.exists" =>
                "Something's wrong with your submission. Refresh the page and try submitting again.",
        ];

        $attributes = [
            "lang" => "language",
            "defs.*.text" => "definition",
            "defs.*.examples.*" => "example",
            "defs.*.comment" => "comment",
        ];

        return Validator::make($input, $rules, $messages, $attributes);
    }
}
