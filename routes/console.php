<?php

use App\Models\User;
use App\Notifications\WeeklyDigest;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("app:send-digest", function () {
    $users = User::query()->where("weekly_digest_enabled", true)->get();

    foreach ($users as $user) {
        $user->notifyNow(new WeeklyDigest());
    }
})
    ->purpose("...")
    ->weeklyOn(/* Saturday */ 6, "12:00");
