<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Definition $definition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Definition $definition)
    {
        //
    }

    /**
     * Upsert the specified resource in storage.
     */
    public function upsert(Request $request, Term $term, Definition $definition)
    {
        // TODO: authorize only the def owner

        $validated = $request->validate([
            "definition" => "required|max:255",
            "examples" => "required|array|min:1|max:5",
            "examples.*" => "required|max:255",
            "comment" => "max:255",
        ]);

        DB::transaction(function () use ($validated, $term, $definition) {
            $definition->definition = $validated["definition"];
            $definition->comment = $validated["comment"];
            // This is necessary if the def is new (i.e. $definition.exists === false).
            $definition->term_id = $term->id;
            $definition->save();

            // TODO: consider using a JSON array for the examples.
            Example::query()
                ->where("definition_id", $definition->id)
                ->delete();

            foreach ($validated["examples"] as $validatedExample) {
                $example = new Example();
                $example->example = $validatedExample;
                $example->definition_id = $definition->id;
                $example->save();
            }
        });

        return redirect(route("terms.show", ["term" => $definition->term_id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Definition $definition)
    {
        // TODO: authorize only the def owner.
        $definition->delete();
        return redirect(route("terms.show", ["term" => $definition->term_id]));
    }
}
