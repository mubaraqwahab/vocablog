<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:255"],
            "_intent" => ["string"],
        ]);

        $request->user()->name = $validated["name"];
        $request->user()->save();

        return Arr::get($validated, "_intent") === "complete"
            ? redirect(rroute("terms.index"))
            : redirect(rroute("profile.edit"))->with(
                "status",
                "profile-updated"
            );
    }
}
