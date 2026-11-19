<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the forgot-password page.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Email a password reset link to the user.
     *
     * The response is the same whether or not an account uses the email address,
     * so the form cannot be used to find out who has an account.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', "If an account uses that email address, we've sent it a link to reset the password.");
    }
}
