<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SettingsController extends Controller
{
    public function edit()
    {
        return view("settings");
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["max:255"],
        ]);

        $user = $request->user();
        $user->name = Arr::get($validated, "name");
        $user->weekly_digest_enabled = $request->has("weekly_digest_enabled");
        $user->save();

        return redirect(rroute("settings.edit"))->with(
            "status",
            "settings-updated"
        );
    }
}
