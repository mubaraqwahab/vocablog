<?php

namespace App\Http\Controllers;

use App\Models\Definition;
use App\Models\Example;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DefinitionController extends Controller
{
    /**
     * Upsert the specified resource in storage.
     */
    public function upsert(Request $request, Term $term, Definition $definition)
    {
        Gate::allowIf(fn(User $user) => $user->is($term->owner));

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
        Gate::allowIf(fn(User $user) => $user->is($definition->term->owner));

        $definition->delete();
        return redirect(route("terms.show", ["term" => $definition->term_id]));
    }
}
