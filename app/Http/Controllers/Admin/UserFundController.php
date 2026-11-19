<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FundUserRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AccountFunded;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Number;

class UserFundController extends Controller
{
    /**
     * Add funds to a user's balance as a completed credit, and tell the user.
     */
    public function store(FundUserRequest $request, User $user): RedirectResponse
    {
        $transaction = new Transaction([
            'type' => TransactionType::Credit,
            'status' => TransactionStatus::Completed,
            'amount_in_cents' => $request->amountInCents(),
            'description' => $request->validated('description'),
        ]);

        $transaction->admin()->associate($request->user('admin'));

        $user->transactions()->save($transaction);

        $user->notify(new AccountFunded($transaction));

        $amount = Number::currency($transaction->amount_in_cents / 100);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', "Added {$amount} to {$user->name}'s balance.");
    }
}
