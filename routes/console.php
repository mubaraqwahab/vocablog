<?php

use App\Models\User;
use App\Notifications\WeeklyDigest;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("app:send-digest", function () {
    $termsLoader = function ($query) {
        $query
            ->withWhereHas("definitions", function ($query) {
                $query->whereBetween("created_at", [now()->addWeeks(-1), now()]);
            })
            ->orderBy("name", "asc");
    };

    $users = User::query()
        ->where("weekly_digest_enabled", true)
        ->with(["terms" => $termsLoader])
        ->get();

    foreach ($users as $user) {
        $user->notifyNow(new WeeklyDigest());
    }
})
    ->purpose("Send a weekly digest to all users")
    ->weeklyOn(Schedule::SUNDAY, "12:00");
