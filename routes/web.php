<?php

use App\Http\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect("/terms");
});

Route::resource("terms", TermController::class);
