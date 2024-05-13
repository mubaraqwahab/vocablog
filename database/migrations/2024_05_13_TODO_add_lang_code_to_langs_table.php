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
        Schema::table("terms", function (Blueprint $table) {
            $table->dropForeign('lang_id');
            $table->string('lang_id')->change();
        });

        Schema::table("langs", function (Blueprint $table) {
            $table->string('id')->change();
        });

        Schema::table("terms", function (Blueprint $table) {
            $table
                ->foreign("lang_id")
                ->references('id')
                ->on('langs')
                ->constrained()
                ->cascadeOnUpdate()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // TODO
    }
};

// TODO:
// 1. Drop foreign lang_id and change to string column
// 2. Change primary id to a string column
// 3. Return foreign key to lang_id
// 3. Update the primary id values
// 4. Update model config that expects id to be int
