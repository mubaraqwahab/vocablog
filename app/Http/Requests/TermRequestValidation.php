<?php

namespace App\Http\Requests;

use App\Models\Term;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

trait TermRequestValidation
{
    public function makeRules(?Term $term = null): array
    {
        $uniqueTermRule = Rule::unique("terms", "name")
            ->where(
                fn(Builder $query) => $query
                    ->where("owner_id", request()->user()->id)
                    ->where("lang_id", request()->input("lang"))
            )
            ->when($term, fn(Unique $rule) => $rule->ignoreModel($term));

        $existingDefRule = Rule::exists("definitions", "id")->where(
            fn(Builder $query) => $query->where("term_id", $term->id)
        );

        return [
            "term" => ["required", "max:255", $uniqueTermRule],
            "lang" => ["required", "exists:langs,id"],
            "defs" => ["required", "array", "min:1"],
            ...$term ? ["defs.*.id" => ["nullable", "integer", $existingDefRule]] : [],
            "defs.*.text" => ["required", "max:255"],
            "defs.*.examples" => ["array", "max:3"],
            "defs.*.examples.*" => ["required", "max:255"],
            "defs.*.comment" => ["max:255"],
        ];
    }

    public function messages(): array
    {
        return [
            "term.unique" => "You already have this :attribute.",
            // This rule should only fail if the page's DOM has been tampered with (or something similar)
            "defs.*.id.exists" =>
                "Something's wrong with your submission. Refresh the page and try submitting again.",
        ];
    }

    public function attributes(): array
    {
        return [
            "lang" => "language",
            "defs.*.text" => "definition",
            "defs.*.examples.*" => "example",
            "defs.*.comment" => "comment",
        ];
    }
}
