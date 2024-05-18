<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("login_links", function (Blueprint $table) {
            $table->id();
            $table->string("url")->unique();
            // I'm not creating a model for this table,
            // so I'll set the default created_at here.
            $table->timestamp("created_at")->default(new Expression("now()"));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("login_links");
    }
};
