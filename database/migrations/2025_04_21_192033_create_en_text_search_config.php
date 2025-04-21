<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("create text search configuration en ( copy = english )");
        DB::statement(
            "alter text search configuration en" .
                " alter mapping for hword, hword_part, word" .
                " with unaccent, english_stem"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("drop text search configuration if exists en cascade");
    }
};
