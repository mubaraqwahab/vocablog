<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("digest", function () {
    $users = User::query()
        ->where("weekly_digest_enabled", true)
        ->with([
            "terms" => function (HasMany $relation) {
                $relation
                    ->getQuery()
                    ->with(["definitions"])
                    ->join("definitions", "definitions.term_id", "=", "terms.id")
                    ->whereBetween("definitions.created_at", [now()->addWeeks(-1), now()])
                    ->orderBy("definitions.created_at")
                    ->select(["terms.*"]);
            },
        ])
        ->get();

    foreach ($users as $user) {
        // TODO: send email to user (with user->terms in email body)
    }
})->weeklyOn("Saturday", "12:00");
