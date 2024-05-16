<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

function uniqueTermRule()
{
    return Rule::unique("terms", "name")->where(function (Builder $query) {
        $request = request();
        return $query
            ->where("owner_id", $request->user()->id)
            ->where("lang_id", $request->input("lang"));
    });
}

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
        $validated = $request->validate(
            [
                "term" => ["required", "max:255", uniqueTermRule()],
                "lang" => ["required", "exists:langs,id"],
                "defs" => ["required", "array", "min:1"],
                "defs.*.text" => ["required", "max:255"],
                "defs.*.examples" => ["array", "max:3"],
                "defs.*.examples.*" => ["required", "max:255"],
                "defs.*.comment" => ["max:255"],
            ],
            ["term.unique" => "You already have this term in your Vocablog."]
        );

        DB::transaction(function () use ($validated, $request) {
            $term = new Term();
            $term->name = $validated["term"];
            $term->lang_id = $validated["lang"];
            $term->owner_id = $request->user()->id;
            $term->save();

            foreach ($validated["defs"] as $validatedDef) {
                $def = new Definition();
                $def->text = $validatedDef["text"];
                $def->comment = $validatedDef["comment"];
                $def->term_id = $term->id;
                $def->save();

                $validatedDef["examples"] ??= [];
                foreach ($validatedDef["examples"] as $validatedExample) {
                    $example = new Example();
                    $example->text = $validatedExample;
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
        return view("terms.show", ["term" => $term, "lang" => $lang]);
    }

    public function edit(Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions.examples");
        $langs = Lang::query()->orderBy("name", "asc")->get();

        $emptyDef = ["text" => "", "examples" => [], "comment" => ""];
        $defs = $term->definitions->map(function ($def) {
            return [
                "text" => $def->text,
                "examples" => $def->examples->map(fn($ex) => $ex->text),
                "comment" => $def->comment,
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
                    uniqueTermRule()->ignoreModel($term),
                ],
                "lang" => ["required", "exists:langs,id"],
                "defs" => ["required", "array", "min:1"],
                "defs.*.text" => ["required", "max:255"],
                "defs.*.examples" => ["array", "max:3"],
                "defs.*.examples.*" => ["required", "max:255"],
                "defs.*.comment" => ["max:255"],
            ],
            ["term.unique" => "You already have this term in your Vocablog."]
        );

        DB::transaction(function () use ($validated, $term) {
            $term->name = $validated["term"];
            $term->lang_id = $validated["lang"];
            // If only a def or example is updated, let the time reflect on the term.
            // (Laravel won't update the DB if the model isn't dirty.)
            $term->updated_at = now();
            $term->save();

            Definition::query()
                ->where("term_id", $term->id)
                ->delete();

            foreach ($validated["defs"] as $validatedDef) {
                $def = new Definition();
                $def->text = $validatedDef["text"];
                $def->comment = $validatedDef["comment"];
                $def->term_id = $term->id;
                $def->save();

                $validatedDef["examples"] ??= [];
                foreach ($validatedDef["examples"] as $validatedExample) {
                    $example = new Example();
                    $example->text = $validatedExample;
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
