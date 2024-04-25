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
        Schema::create("expressions", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("expression");
            $table->foreignId("lang_id")->constrained();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->unique(["expression", "lang_id", "user_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("expressions");
    }
};
