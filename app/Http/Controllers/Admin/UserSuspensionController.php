<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserSuspensionController extends Controller
{
    /**
     * Suspend a user, which signs them out and stops them from signing back in.
     */
    public function store(User $user): RedirectResponse
    {
        if (! $user->isSuspended()) {
            $user->forceFill(['suspended_at' => now()])->save();
        }

        return back()->with('status', "{$user->name}'s account is suspended.");
    }

    /**
     * Reactivate a suspended user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->forceFill(['suspended_at' => null])->save();

        return back()->with('status', "{$user->name}'s account is active again.");
    }
}
