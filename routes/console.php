<?php

use App\Models\Definition;
use App\Models\Term;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("migdata:examples", function () {
    $results = DB::table("examples")
        ->groupBy("definition_id")
        ->select(["definition_id"])
        ->selectRaw("jsonb_agg(text) as examples")
        ->get();

    foreach ($results as $result) {
        $def = Definition::query()->find($result->definition_id);
        $def->examples = json_decode($result->examples);
        $def->save();
    }
})->purpose(
    "Migrate the data in the examples table to " .
        "the examples column of the definitions table."
);

Artisan::command("migdata:defs_num", function () {
    $terms = Term::all();

    foreach ($terms as $term) {
        $defs = Definition::query()
            ->where("term_id", $term->id)
            ->orderBy("id", "asc")
            ->get();
        foreach ($defs as $key => $def) {
            $def->index = $key;
            $def->save();
        }
    }
})->purpose("Add indices to definitions.");
