<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    /**
     * Sign suspended users out as soon as they make another request.
     *
     * Background requests, such as the chat refreshing itself, get a 403 instead of a
     * redirect so the sign-in page never renders inside the chat.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user('web')?->isSuspended()) {
            return $next($request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->ajax() || $request->expectsJson()) {
            abort(403, 'This account has been suspended.');
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Your account has been suspended. Contact support if you think this is a mistake.',
        ]);
    }
}
