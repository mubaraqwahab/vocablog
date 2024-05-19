<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Query\Builder;
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
        $terms = Term::query()
            ->withCount("definitions")
            ->where("owner_id", $request->user()->id)
            ->latest("updated_at")
            ->paginate();

        return view("terms.index", ["terms" => $terms]);
    }

    public function create()
    {
        $langs = Lang::query()->orderBy("name", "asc")->get();
        $emptyDef = ["definition" => "", "examples" => [], "comment" => ""];

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

            $this->saveDefs($term, $validated["defs"]);
        });

        return redirect(rroute("terms.index"));
    }

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

        $validated = $this->validator($request->input(), $term)->validate();

        DB::transaction(function () use ($validated, $term) {
            $term->name = $validated["term"];
            $term->lang_id = $validated["lang"];
            // If only a def or example is updated, let the time reflect on the term.
            // (Laravel won't update the DB if the model isn't dirty.)
            $term->touch();
            $term->save();

            $this->saveDefs($term, $validated["defs"]);
        });

        return redirect(
            rroute("terms.show", ["term" => $term, "lang" => $lang])
        );
    }

    public function destroy(Lang $lang, Term $term)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

        $term->delete();

        return redirect(rroute("terms.index"));
    }

    protected function validator(array $input, Term $term = null)
    {
        $uniqueTermRule = Rule::unique("terms", "name")->where(
            fn(Builder $query) => $query
                ->where("owner_id", request()->user()->id)
                ->where("lang_id", request()->input("lang"))
        );

        $rules = [
            "term" => [
                "required",
                "max:255",
                $term ? $uniqueTermRule->ignoreModel($term) : $uniqueTermRule,
            ],
            "lang" => ["required", "exists:langs,id"],
            "defs" => ["required", "array", "min:1"],
            "defs.*.text" => ["required", "max:255"],
            "defs.*.examples" => ["array", "max:3"],
            "defs.*.examples.*" => ["required", "max:255"],
            "defs.*.comment" => ["max:255"],
        ];

        $messages = [
            "term.unique" => "You already have this term.",
        ];

        return Validator::make($input, $rules, $messages);
    }

    protected function saveDefs(Term $term, array $rawDefs)
    {
        // Definition::query()->where("term_id", $termId)->delete();

        foreach ($rawDefs as $rawDef) {
            $term->definitions()->updateOrCreate(
                ["serial_num" => $rawDef["serial_num"]],
                [
                    "text" => $rawDef["text"],
                    "comment" => Arr::get($rawDef, "comment"),
                    "serial_num" => $rawDef["serial_num"],
                ]
            );

            $def = new Definition();
            $def->text = $rawDef["text"];
            $def->comment = Arr::get($rawDef, "comment");
            $def->term_id = $term->id;
            $def->save();

            $rawDef["examples"] ??= [];
            foreach ($rawDef["examples"] as $rawExample) {
                $example = new Example();
                $example->text = $rawExample;
                $example->definition_id = $def->id;
                $example->save();
            }
        }
    }
}
