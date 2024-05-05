<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("terms.index"));
});

Route::view("dashboard", "dashboard")->name("dashboard");

Route::view("welcome", "welcome")->name("welcome");

Route::middleware("auth")->group(function () {
    Route::get("profile", [ProfileController::class, "edit"])->name(
        "profile.edit"
    );
    Route::put("profile", [ProfileController::class, "update"])->name(
        "profile.update"
    );
    Route::delete("profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy"
    );
});

Route::resource("terms", TermController::class)->middleware([
    "auth",
    "verified",
]);

require __DIR__ . "/auth.php";
