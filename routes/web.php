<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(rroute("terms.index"));
});

Route::middleware("guest")->group(function () {
    Route::view("login", "login")->name("login");

    // TODO: throttle this?
    Route::post("login", [AuthController::class, "sendLoginLink"]);

    Route::view("check-your-email", "check-your-email")->name("check-your-email");
});

Route::get("verify", [AuthController::class, "store"])
    ->middleware(["signed", "throttle:6,1"])
    ->name("verify");

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
    Route::get("/mail/{name}", function (string $name) {
        if (app()->environment() === "production") {
            abort(404);
        }

        /** @var \Illuminate\Mail\Mailable */
        $mailable = app("App\\Mail\\{$name}", ["url" => request()->fullUrl()]);
        $mailable->to("test@example.com");

        return $mailable;
    });

    Route::get("/notification/{name}", function (string $name) {
        if (app()->environment() === "production") {
            abort(404);
        }

        /** @var \Illuminate\Mail\Mailable */
        $mailable = app("App\\Notifications\\{$name}")->toMail(request()->user());

        return $mailable;
    });
});
