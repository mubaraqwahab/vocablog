<?php

use App\Models\Term;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            "alter table terms add column defs jsonb not null default '[]'::jsonb" .
                " constraint terms_defs_is_json_array check (jsonb_typeof(defs) = 'array')"
        );

        Term::query()
            ->with(["definitions" => ["examples"]])
            ->get()
            ->each(function (Term $term) {
                $term->defs = $term->definitions->map(function ($def) {
                    return [
                        "def" => $def->definition,
                        "comment" => $def->comment ?? "",
                        "examples" => $def->examples->map(function ($ex) {
                            return $ex->example;
                        }),
                    ];
                });
                $term->save();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("terms", function (Blueprint $table) {
            $table->dropColumn("defs");
        });
    }
};
