<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(rroute("terms.index"));
});

Route::middleware("guest")->group(function () {
    Route::view("login", "login")->name("login");

    Route::post("login", [AuthController::class, "sendLoginLink"]);

    Route::view("check-your-email", "check-your-email")->name(
        "check-your-email"
    );
});

Route::get("verify", [AuthController::class, "store"])
    ->middleware(["loginlink", "throttle:6,1"])
    ->name("verify");

Route::middleware("auth")->group(function () {
    Route::view("profile", "profile")->name("profile.edit");

    Route::patch("profile", [ProfileController::class, "update"])->name(
        "profile.update"
    );

    Route::view("complete-profile", "complete-profile")->name(
        "profile.complete"
    );

    Route::post("logout", [AuthController::class, "destroy"])->name("logout");
});

Route::middleware(["auth", "verified"])->group(function () {
    Route::get("terms/{lang}/{term}", [TermController::class, "show"])
        ->name("terms.show")
        ->scopeBindings();

    Route::get("terms/{lang}/{term}/edit", [TermController::class, "edit"])
        ->name("terms.edit")
        ->scopeBindings();

    Route::put("terms/{lang}/{term}", [TermController::class, "update"])
        ->name("terms.update")
        ->scopeBindings();

    Route::delete("terms/{lang}/{term}", [TermController::class, "destroy"])
        ->name("terms.destroy")
        ->scopeBindings();

    Route::resource("terms", TermController::class)->only([
        "index",
        "create",
        "store",
    ]);
});
