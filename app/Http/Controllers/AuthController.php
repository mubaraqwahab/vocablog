<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\LoginLink;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function sendLoginLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "email" => ["required", "email"],
        ]);

        $email = $validated["email"];
        $intended = omit_origin(redirect()->intended()->getTargetUrl());

        // NOTE: The URL can be reused multiple times until it expires. Is that bad?
        $signedUrl = URL::temporarySignedRoute(
            "verify",
            expiration: now()->addMinutes(30),
            parameters: ["email" => $email, "intended" => $intended]
        );

        // TODO: fix this http(s) issue once and for all!
        if (app()->environment() === "production") {
            $signedUrl = str_replace("http://", "https://", $signedUrl);
        }

        Mail::to($email)->sendNow(new LoginLink($signedUrl));

        return redirect(route("check-your-email"));
    }

    public function store(Request $request): RedirectResponse
    {
        $email = $request->query("email");
        $user = User::query()->firstOrCreate(["email" => $email]);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        session()->put("url.intended", $request->query("intended"));

        return $user->wasRecentlyCreated
            ? redirect(rroute("complete-profile.edit"))
            : redirect()->intended(route("terms.index"));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route("index"));
    }
}
