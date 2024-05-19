<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompleteProfileController extends Controller
{
    public function edit()
    {
        return view("complete-profile");
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["required", "max:255"],
        ]);

        $request->user()->name = $validated["name"];
        $request->user()->save();

        return redirect(rroute("terms.index"));
    }
}
