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

class AuthenticatedSessionController extends Controller
{
    public function sendLoginLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "email" => ["required", "email"],
        ]);

        $email = $validated["email"];

        $url = URL::temporarySignedRoute("verify", now()->addMinutes(30), [
            "email" => $email,
            "intended" => redirect()->intended()->getTargetUrl(),
        ]);

        // TODO: queue this
        Mail::to($email)->sendNow(new LoginLink($url));

        return redirect(rroute("check-your-email"));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request, string $email): RedirectResponse
    {
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
