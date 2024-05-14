<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("terms", function (Blueprint $table) {
            $table->dropForeign(["lang_id"]);
        });

        // For some reason, when I put this and the above together,
        // the generated SQL statements aren't in my intended order.
        Schema::table("terms", function (Blueprint $table) {
            $table->string("lang_id")->change();
        });

        Schema::table("langs", function (Blueprint $table) {
            $table->string("id")->change();
        });

        DB::statement("drop sequence if exists langs_id_seq");

        Schema::table("terms", function (Blueprint $table) {
            $table
                ->foreign("lang_id")
                ->references("id")
                ->on("langs")
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("terms", function (Blueprint $table) {
            $table->dropForeign(["lang_id"]);
        });

        Schema::table("terms", function (Blueprint $table) {
            $table->unsignedBigInteger("lang_id")->change();
        });

        DB::statement(
            "create sequence langs_id_seq start with 1 increment by 1 no minvalue no maxvalue cache 1"
        );

        Schema::table("langs", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("id")
                ->default(new Expression("nextval('langs_id_seq'::regclass)"))
                ->change();
        });

        DB::statement("alter sequence langs_id_seq owned by langs.id");

        Schema::table("terms", function (Blueprint $table) {
            $table->foreign("lang_id")->references("id")->on("langs");
        });
    }
};
