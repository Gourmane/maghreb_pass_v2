<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithApiCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken() && $request->cookies->has('maghrebpass_token')) {
            $request->headers->set('Authorization', 'Bearer '.$request->cookie('maghrebpass_token'));
        }

        return $next($request);
    }
}
