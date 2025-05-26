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
        Schema::table("langs", function (Blueprint $table) {
            $table
                ->string("id")
                ->comment(
                    "The shortest ISO 639 code for the language." .
                        " The value here should be suitable for use as the `lang` attribute in an HTML doc."
                )
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("langs", function (Blueprint $table) {
            $table->string("id")->change();
        });
    }
};
