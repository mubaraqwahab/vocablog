<?php

use Illuminate\Database\Migrations\Migration;
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
            $table->foreign("lang_id")->references("id")->on("langs")->cascadeOnUpdate();
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

        DB::statement(
            "alter table terms alter column lang_id type bigint using lang_id::bigint" .
                ", alter column lang_id set not null" .
                ", alter column lang_id drop default" .
                ", alter column lang_id drop identity if exists"
        );

        DB::statement(
            "create sequence langs_id_seq start with 1 increment by 1 no minvalue no maxvalue cache 1"
        );

        DB::statement(
            "alter table langs alter column id type bigint using id::bigint" .
                ", alter column id set not null" .
                ", alter column id set default nextval('langs_id_seq'::regclass)" .
                ", alter column id drop identity if exists"
        );

        DB::statement("alter sequence langs_id_seq owned by langs.id");

        Schema::table("terms", function (Blueprint $table) {
            $table->foreign("lang_id")->references("id")->on("langs");
        });
    }
};
