<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
            ->paginate();

        return view("terms.index", ["terms" => $terms]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $langs = Lang::query()->orderBy("name", "asc")->get();
        return view("terms.create", ["langs" => $langs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "term" => ["required", "max:255", "unique:terms"],
            "lang" => ["required", "exists:langs,id"],
            "defs" => ["required", "array", "min:1"],
            "defs.*.definition" => ["required", "max:255"],
            "defs.*.examples" => ["array", "max:3"],
            "defs.*.examples.*" => ["required", "max:255"],
            "defs.*.comment" => ["max:255"],
        ]);

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
    public function show(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions.examples");
        return view("terms.show", ["term" => $term]);
    }

    public function edit(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->load("definitions.examples");
        $langs = Lang::query()->orderBy("name", "asc")->get();
        return view("terms.edit", ["term" => $term, "langs" => $langs]);
    }

    public function update(Request $request, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $validated = $request->validate([
            "term" => [
                "required",
                "max:255",
                // TODO: terms+lang combination should be unique
                Rule::unique("terms")->ignore($term->id),
            ],
            "lang" => ["required", "exists:langs,id"],
            "defs" => ["required", "array", "min:1"],
            "defs.*.definition" => ["required", "max:255"],
            "defs.*.examples" => ["array", "max:3"],
            "defs.*.examples.*" => ["required", "max:255"],
            "defs.*.comment" => ["max:255"],
        ]);

        DB::transaction(function () use ($validated, $term) {
            $term->term = $validated["term"];
            $term->lang_id = $validated["lang"];
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

        return redirect(rroute("terms.show", ["term" => $term]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->delete();
        return redirect(rroute("terms.index"));
    }
}
