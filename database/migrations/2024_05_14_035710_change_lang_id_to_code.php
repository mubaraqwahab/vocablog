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
            $table->dropForeign("lang_id");
            $table->string("lang_id")->change();
        });

        Schema::table("langs2", function (Blueprint $table) {
            $table->string("id")->change();
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
