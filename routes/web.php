<?php

use App\Http\Controllers\DefinitionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect(route("terms.index"));
});

Route::resource("profile", ProfileController::class)
    ->only(["edit", "update", "destroy"])
    ->middleware("auth");

Route::middleware(["auth", "verified"])->group(function () {
    Route::resource("terms", TermController::class)->except(["edit", "update"]);

    Route::put("terms/{term}/definitions/{definition?}", [
        DefinitionController::class,
        "upsert",
    ])->name("definitions.upsert");

    Route::delete("definitions/{definition}", [
        DefinitionController::class,
        "destroy",
    ])->name("definitions.destroy");
});

require __DIR__ . "/auth.php";
