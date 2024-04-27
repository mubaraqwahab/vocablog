<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Lang;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\ComponentAttributeBag;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User */
        $user = $request->user();
        $terms = Term::query()
            ->withCount("definitions")
            ->whereBelongsTo($user)
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
            "term" => "required|max:255",
            "lang" => "required",
            "defs" => "required|array|min:1",
            "defs.*.definition" => "required|max:255",
            "defs.*.examples" => "required|array|min:1|max:5",
            "defs.*.examples.*" => "required|max:255",
            "defs.*.comment" => "max:255",
        ]);

        DB::transaction(function () use ($validated, $request) {
            $term = new Term();
            $term->term = $validated["term"];
            $term->lang_id = $validated["lang"];
            $term->user_id = $request->user()->id;
            $term->save();

            foreach ($validated["defs"] as $validatedDef) {
                $def = new Definition();
                $def->definition = $validatedDef["definition"];
                $def->comment = $validatedDef["comment"];
                $def->term_id = $term->id;
                $def->save();

                foreach ($validatedDef["examples"] as $validatedExample) {
                    $example = new Example();
                    $example->example = $validatedExample;
                    $example->definition_id = $def->id;
                    $example->save();
                }
            }
        });

        return redirect(route("terms.index"));
    }

    /**
     * Display the specified resource.
     */
    public function show(Term $term)
    {
        // TODO: authorize only the term owner

        $term->load("definitions.examples");
        return view("terms.show", ["term" => $term]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Term $term)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Term $term)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        // TODO: authorize only the term owner
    }
}
