<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ValidateLoginLink
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasValidSignature()) {
            throw new InvalidSignatureException();
        }

        $count = DB::table("login_links")
            ->where("url", $request->fullUrl())
            ->delete();

        if ($count) {
            return $next($request);
        }

        throw new InvalidSignatureException();
    }
}
