<?php

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
        Schema::table("definitions", function (Blueprint $table) {
            $table->jsonb("examples")->default("[]");
        });

        DB::statement(
            "alter table definitions add constraint definitions_examples_is_array " .
                "check (jsonb_typeof(examples) = 'array')"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("definitions", function (Blueprint $table) {
            $table->dropColumn(["examples"]);
        });
    }
};
