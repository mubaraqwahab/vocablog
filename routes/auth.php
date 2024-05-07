<?php

use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::view("login", "login")->name("login");

    Route::post("login", [
        AuthenticatedSessionController::class,
        "sendLoginLink",
    ]);

    Route::view("check-your-email", "check-your-email")->name(
        "check-your-email"
    );

    Route::get("verify/{email}", [
        AuthenticatedSessionController::class,
        "store",
    ])
        ->middleware(["signed", "throttle:6,1"])
        ->name("verify");
});

Route::middleware("auth")->group(function () {
    Route::view("new-user", "new-user")->name("new-user");

    Route::post("new-user", function (Request $request) {
        $validated = $request->validate(["name" => ["required", "max:255"]]);
        $user = $request->user();
        $user->name = $validated["name"];
        $user->save();
        return redirect(rroute("terms.index"));
    });

    Route::post("logout", [
        AuthenticatedSessionController::class,
        "destroy",
    ])->name("logout");
});
