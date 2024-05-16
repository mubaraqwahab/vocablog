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
            $table->dropTimestamps();
            $table->renameColumn("definition", "text");
        });

        Schema::table("examples", function (Blueprint $table) {
            $table->renameColumn("example", "text");
        });

        Schema::table("terms", function (Blueprint $table) {
            $table->renameColumn("term", "name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("definitions", function (Blueprint $table) {
            $table->timestamps();
            $table->renameColumn("text", "definition");
        });

        Schema::table("examples", function (Blueprint $table) {
            $table->renameColumn("text", "example");
        });

        Schema::table("terms", function (Blueprint $table) {
            $table->renameColumn("name", "term");
        });
    }
};
