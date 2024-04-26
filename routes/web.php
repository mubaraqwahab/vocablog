<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("terms.index"));
});

Route::middleware("auth")->group(function () {
    Route::resource("profile", ProfileController::class)->only([
        "edit",
        "update",
        "destroy",
    ]);

    Route::resource("terms", TermController::class)->middleware("verified");
});

require __DIR__ . "/auth.php";
