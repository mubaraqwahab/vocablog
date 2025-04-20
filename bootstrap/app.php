<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up"
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->redirectUsersTo(fn(Request $request) => route("terms.index"))
            ->redirectGuestsTo(fn(Request $request) => route("index"));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
