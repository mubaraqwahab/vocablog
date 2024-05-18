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

        dump($request, $request->fullUrl());

        $allLinks = DB::table("login_links")->get(["url"]);
        dump($allLinks);

        $count = DB::table("login_links")
            ->where("url", $request->fullUrl())
            ->dumpRawSql()
            ->delete();

        dump($count);

        if ($count) {
            return $next($request);
        }

        throw new InvalidSignatureException();
    }
}
