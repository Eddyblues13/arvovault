<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the platform overview for admins.
     */
    public function __invoke(): View
    {
        $totalCreditedInCents = (int) Transaction::query()->credits()->completed()->sum('amount_in_cents');
        $totalWithdrawnInCents = (int) Transaction::query()->withdrawals()->completed()->sum('amount_in_cents');
        $pendingWithdrawalsInCents = (int) Transaction::query()->withdrawals()->pending()->sum('amount_in_cents');

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'balancesHeldInCents' => $totalCreditedInCents - $totalWithdrawnInCents - $pendingWithdrawalsInCents,
            'totalCreditedInCents' => $totalCreditedInCents,
            'totalWithdrawnInCents' => $totalWithdrawnInCents,
            'pendingTransactionsCount' => Transaction::query()->pending()->count(),
            'recentTransactions' => Transaction::query()->with(['user', 'admin'])->latest()->latest('id')->limit(6)->get(),
            'recentUsers' => User::query()->withAvailableBalance()->latest()->latest('id')->limit(5)->get(),
        ]);
    }
}
