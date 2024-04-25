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
        Schema::create("definitions", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("definition");
            $table->string("comment");
            $table
                ->foreignId("expression_id")
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("definitions");
    }
};
