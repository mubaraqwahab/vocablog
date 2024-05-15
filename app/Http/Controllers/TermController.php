<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $terms = Term::query()
            ->withCount("definitions")
            ->whereBelongsTo($request->user(), "owner")
            ->latest("updated_at")
            ->paginate();

        return view("terms.index", ["terms" => $terms]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $langs = Lang::query()->orderBy("name", "asc")->get();
        $emptyDef = ["definition" => "", "examples" => [], "comment" => ""];

        return view("terms.create", [
            "langs" => $langs,
            "emptyDef" => $emptyDef,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "term" => [
                    "required",
                    "max:255",
                    Rule::unique("terms")->where(function ($query) use (
                        $request
                    ) {
                        return $query
                            ->where("owner_id", $request->user()->id)
                            ->where("lang_id", $request->input("lang"));
                    }),
                ],
                "lang" => ["required", "exists:langs,id"],
                "defs" => ["required", "array", "min:1"],
                "defs.*.definition" => ["required", "max:255"],
                "defs.*.examples" => ["array", "max:3"],
                "defs.*.examples.*" => ["required", "max:255"],
                "defs.*.comment" => ["max:255"],
            ],
            [
                "term.unique" =>
                    "You already have an existing term from the same language in your Vocablog. You might want to edit that term instead.",
            ]
        )->after(function () {});

        $validated = $validator->validate();

        DB::transaction(function () use ($validated, $request) {
            $term = new Term();
            $term->term = $validated["term"];
            $term->lang_id = $validated["lang"];
            $term->owner_id = $request->user()->id;
            $term->save();

            foreach ($validated["defs"] as $validatedDef) {
                $def = new Definition();
                $def->definition = $validatedDef["definition"];
                $def->comment = $validatedDef["comment"];
                $def->term_id = $term->id;
                $def->save();

                $validatedDef["examples"] ??= [];
                foreach ($validatedDef["examples"] as $validatedExample) {
                    $example = new Example();
                    $example->example = $validatedExample;
                    $example->definition_id = $def->id;
                    $example->save();
                }
            }
        });

        return redirect(rroute("terms.index"));
    }

    /**
     * Display the specified resource.
     */
    public function show(Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions.examples");
        return view("terms.show", ["term" => $term]);
    }

    public function edit(Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions.examples");
        $langs = Lang::query()->orderBy("name", "asc")->get();

        $emptyDef = ["definition" => "", "examples" => [], "comment" => ""];
        $defs = $term->definitions->map(function ($def) {
            return [
                "definition" => $def->definition,
                "comment" => $def->comment,
                "examples" => $def->examples->map(function ($ex) {
                    return $ex->example;
                }),
            ];
        });

        return view("terms.edit", [
            "term" => $term,
            "langs" => $langs,
            "emptyDef" => $emptyDef,
            "defs" => $defs,
        ]);
    }

    public function update(Request $request, Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $validated = $request->validate(
            [
                "term" => [
                    "required",
                    "max:255",
                    Rule::unique("terms")
                        ->where(function ($query) use ($request) {
                            return $query
                                ->where("owner_id", $request->user()->id)
                                ->where("lang_id", $request->input("lang"));
                        })
                        ->ignoreModel($term),
                ],
                "lang" => ["required", "exists:langs,id"],
                "defs" => ["required", "array", "min:1"],
                "defs.*.definition" => ["required", "max:255"],
                "defs.*.examples" => ["array", "max:3"],
                "defs.*.examples.*" => ["required", "max:255"],
                "defs.*.comment" => ["max:255"],
            ],
            [
                "term.unique" =>
                    "You already have an existing term for the same language in your Vocablog. You might want to edit that term instead.",
            ]
        );

        DB::transaction(function () use ($validated, $term) {
            $term->term = $validated["term"];
            $term->lang_id = $validated["lang"];
            // If a only def or example is updated, let the time reflect on the term too.
            // (Laravel won't update the DB if the model isn't dirty.)
            $term->updated_at = now();
            $term->save();

            // TODO: consider using a JSON array for the defs.
            Definition::query()
                ->where("term_id", $term->id)
                ->delete();

            foreach ($validated["defs"] as $validatedDef) {
                $def = new Definition();
                $def->definition = $validatedDef["definition"];
                $def->comment = $validatedDef["comment"];
                $def->term_id = $term->id;
                $def->save();

                $validatedDef["examples"] ??= [];
                foreach ($validatedDef["examples"] as $validatedExample) {
                    $example = new Example();
                    $example->example = $validatedExample;
                    $example->definition_id = $def->id;
                    $example->save();
                }
            }
        });

        return redirect(
            rroute("terms.show", ["term" => $term, "lang" => $lang])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->delete();
        return redirect(rroute("terms.index"));
    }
}
