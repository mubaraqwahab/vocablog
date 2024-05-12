<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
        ]);

        $request->user()->name = $validated["name"];
        $request->user()->save();

        return $request->routeIs("profile.complete")
            ? redirect(rroute("terms.index"))
            : redirect(rroute("profile.edit"))->with(
                "status",
                "profile-updated"
            );
    }
}
