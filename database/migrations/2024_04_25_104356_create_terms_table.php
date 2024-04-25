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
        Schema::create("terms", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("term");
            $table->foreignId("lang_id")->constrained();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->unique(["term", "lang_id", "user_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("terms");
    }
};
