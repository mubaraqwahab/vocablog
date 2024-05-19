<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("definitions", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("num")
                ->nullable(false)
                ->comment(
                    "The zero-based index of the definition within the definitions of its associated term"
                )
                ->change();
            $table->renameColumn("num", "index");
            $table->renameIndex(
                "definitions_term_id_num_unique",
                "definitions_term_id_index_unique"
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("definitions", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("index")
                ->nullable()
                ->comment(
                    "The serial number of the definition within it's associated term"
                )
                ->change();
            $table->renameColumn("index", "num");
            $table->renameIndex(
                "definitions_term_id_index_unique",
                "definitions_term_id_num_unique"
            );
        });
    }
};
