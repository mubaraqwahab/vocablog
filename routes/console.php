<?php

use App\Mail\WeeklyDigest;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

Artisan::command("app:send-digest", function () {
    $users = User::query()
        ->where("weekly_digest_enabled", true)
        ->withWhereHas("terms", function ($query) {
            $query
                ->withWhereHas("definitions", function ($query) {
                    $query->whereBetween("created_at", [now()->addWeeks(-1), now()]);
                })
                ->orderBy("name", "asc");
        })
        ->orderBy("id", "asc")
        ->paginate(10);

    foreach ($users as $user) {
        Mail::to($user)->sendNow(new WeeklyDigest($user));
    }
})
    ->purpose("Send a weekly digest to all users")
    ->weeklyOn(Schedule::SUNDAY, "12:00");
