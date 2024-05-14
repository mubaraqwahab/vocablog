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
        // Drop foreign key constraint
        // Change type of foreign key attribute
        // Change type of primary key
        // Restore foreign key constraint
        // Update value of primary key

        Schema::table("terms2", function (Blueprint $table) {
            $table->dropForeign(["lang_id"]);
        });

        // For some reason, when I put this and the above together,
        // the generated SQL statements aren't in my intended order.
        Schema::table("terms2", function (Blueprint $table) {
            $table->string("lang_id")->change();
        });

        Schema::table("langs2", function (Blueprint $table) {
            $table->string("id")->change();
        });

        Schema::table("terms2", function (Blueprint $table) {
            $table
                ->foreign("lang_id")
                ->references("id")
                ->on("langs2")
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("langs", function (Blueprint $table) {
            //
        });
    }
};
