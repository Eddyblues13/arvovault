<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the signed-in user's wallet overview.
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
        ]);
    }
}
