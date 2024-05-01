<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("terms.index"));
});

Route::resource("profile", ProfileController::class)
    ->only(["edit", "update", "destroy"])
    ->middleware("auth");

Route::resource("terms", TermController::class)->middleware([
    "auth",
    "verified",
]);

require __DIR__ . "/auth.php";
