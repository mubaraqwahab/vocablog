<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(rroute("terms.index"));
});

Route::view("dashboard", "dashboard")->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("profile", [ProfileController::class, "edit"])->name(
        "profile.edit"
    );
    Route::put("profile", [ProfileController::class, "update"])->name(
        "profile.update"
    );
});

Route::resource("terms", TermController::class)->middleware([
    "auth",
    "verified",
]);

require __DIR__ . "/auth.php";
