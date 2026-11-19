<?php

namespace App\Http\Controllers;

use App\BookingStatus;
use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the signed-in user's wallet overview, next booking and latest tips.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'firstName' => $user->firstName(),
            'availableBalanceInCents' => $user->availableBalanceInCents(),
            'totalCreditedInCents' => $user->totalCreditedInCents(),
            'totalWithdrawnInCents' => $user->totalWithdrawnInCents(),
            'recentTransactions' => $user->transactions()->latest()->latest('id')->limit(5)->get(),
            'nextBooking' => $user->bookings()
                ->where('scheduled_for', '>=', now())
                ->where('status', '!=', BookingStatus::Cancelled)
                ->oldest('scheduled_for')
                ->first(),
            'latestTips' => Tip::query()->published()->latest('published_at')->latest('id')->limit(3)->get(),
        ]);
    }
}
