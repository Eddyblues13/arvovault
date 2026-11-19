<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarkNotificationsAsReadController extends Controller
{
    /**
     * Mark all of the user's notifications as read.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
