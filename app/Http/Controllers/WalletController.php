<?php

namespace App\Http\Controllers;

use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Show the user's balance breakdown and full transaction history.
     *
     * An unknown type filter is ignored, so the history falls back to every transaction.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $type = TransactionType::tryFrom($request->string('type')->toString());

        $transactions = $user->transactions()->latest()->latest('id');

        if ($type !== null) {
            $transactions->where('type', $type);
        }

        return view('wallet', [
            'availableBalanceInCents' => $user->availableBalanceInCents(),
            'totalCreditedInCents' => $user->totalCreditedInCents(),
            'totalWithdrawnInCents' => $user->totalWithdrawnInCents(),
            'pendingWithdrawalsInCents' => $user->pendingWithdrawalsInCents(),
            'pendingCreditsInCents' => $user->pendingCreditsInCents(),
            'type' => $type,
            'transactions' => $transactions->paginate(15)->withQueryString(),
        ]);
    }
}
