<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * Ensure the request comes from a signed-in admin.
     *
     * Guests and signed-in users are sent to the admin sign-in page, or receive a
     * 401 response when they expect JSON.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthenticationException(guards: ['admin'], redirectTo: route('admin.login'));
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
