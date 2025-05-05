<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TermController;
use App\Mail\LoginLink;
use App\Mail\WeeklyDigest;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::view("/", "index")->name("index");

    Route::post("login", [AuthController::class, "sendLoginLink"])
        ->middleware("throttle:6,1")
        ->name("login");

    Route::view("check-your-email", "check-your-email")->name("check-your-email");

    Route::get("verify", [AuthController::class, "store"])
        ->middleware(["signed", "throttle:6,1"])
        ->name("verify");
});

Route::middleware("auth")->group(function () {
    Route::get("settings", [SettingsController::class, "edit"])->name("settings.edit");

    Route::patch("settings", [SettingsController::class, "update"])->name(
        "settings.update"
    );

    Route::get("complete-profile", [CompleteProfileController::class, "edit"])->name(
        "complete-profile.edit"
    );

    Route::patch("complete-profile", [CompleteProfileController::class, "update"])->name(
        "complete-profile.update"
    );

    Route::post("logout", [AuthController::class, "destroy"])->name("logout");
});

Route::middleware(["auth", "verified"])->group(function () {
    Route::resource("terms", TermController::class);
});

Route::prefix("dev")->group(function () {
    Route::get("/mail/login-link", function () {
        if (app()->environment() === "production") {
            abort(404);
        }

        $mailable = new LoginLink(request()->fullUrl());
        $mailable->to("test@example.com");

        return $mailable;
    });

    Route::get("/mail/weekly-digest", function () {
        if (app()->environment() === "production") {
            abort(404);
        }

        $mailable = new WeeklyDigest(request()->user());
        $mailable->to("test@example.com");

        return $mailable;
    });
});
