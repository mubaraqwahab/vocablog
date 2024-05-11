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

        // TODO: document your intent here or refactor the code
        // to express your intent.
        return $request->routeIs("profile.update")
            ? redirect(rroute("profile.edit"))->with(
                "status",
                "profile-updated"
            )
            : redirect(rroute("terms.index"));
    }
}
