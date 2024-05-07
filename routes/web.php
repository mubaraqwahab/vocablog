<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TermController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get("/", function () {
    return redirect(rroute("terms.index"));
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

Route::view("login", "auth.login")->name("login");

Route::post("login", function (Request $request) {
    $validated = $request->validate([
        "email" => ["required", "email"],
    ]);
    $url = URL::temporarySignedRoute("login.verify", now()->addMinutes(30), [
        "email" => $validated["email"],
        "intended" => redirect()->intended()->getTargetUrl(),
    ]);

    // TODO: Send email
    dd($url);

    return redirect(rroute("login.check-your-email"));
})->name("login.submit");

Route::view("login/check-your-email", "auth.check-your-email")->name(
    "login.check-your-email"
);

Route::get("login/verify/{email}", function (Request $request, string $email) {
    $user = User::query()->firstOrCreate(["email" => $email]);
    $user->markEmailAsVerified();

    Auth::login($user, remember: true);
    $request->session()->regenerate();

    session()->put("url.intended", $request->query("intended"));

    if ($user->wasRecentlyCreated) {
        return redirect(rroute("new-user"));
    } else {
        return redirect()->intended(rroute("terms.index"));
    }
})
    ->middleware(["signed", "throttle:6,1"])
    ->name("login.verify");

Route::view("login/new-user", "auth.new-user")->name("login.new-user");

Route::post("login/new-user", function () {
    // TODO: save the name
    // Redirect to intended route
});

require __DIR__ . "/auth.php";
