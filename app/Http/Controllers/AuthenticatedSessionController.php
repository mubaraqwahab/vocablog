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

function omit_origin($url)
{
    $parsed = parse_url($url);
    $omitted =
        $parsed["path"] .
        (isset($parsed["query"]) ? "?{$parsed["query"]}" : "") .
        (isset($parsed["fragment"]) ? "#{$parsed["fragment"]}" : "");
    return $omitted;
}

class AuthenticatedSessionController extends Controller
{
    public function sendLoginLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "email" => ["required", "email"],
        ]);

        $email = $validated["email"];
        $intended = omit_origin(redirect()->intended()->getTargetUrl());

        $signedUrl = URL::temporarySignedRoute(
            "verify",
            expiration: now()->addMinutes(30),
            parameters: ["email" => $email, "intended" => $intended]
        );

        if (app()->environment() === "production") {
            $signedUrl = str_replace("http://", "https://", $signedUrl);
        }

        Mail::to($email)->sendNow(new LoginLink($signedUrl));
        // TODO: what to do if email fails?

        return redirect(rroute("check-your-email"));
    }

    /**
     * Handle an incoming authentication request.
     */
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
            ? redirect(rroute("new-user"))
            : redirect()->intended(rroute("terms.index"));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/");
    }
}
