<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\PayoutMethod;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * Show the payout request form and the user's recent withdrawals.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        return view('withdraw', [
            'availableBalanceInCents' => $user->availableBalanceInCents(),
            'payoutMethods' => PayoutMethod::cases(),
            'withdrawals' => $user->transactions()->withdrawals()->latest()->latest('id')->limit(10)->get(),
        ]);
    }

    /**
     * Request a payout, reserving the amount from the available balance until an admin reviews it.
     *
     * The balance check and the new withdrawal run under a per-user lock, so two requests
     * sent at the same moment cannot spend the same funds twice.
     *
     * @throws ValidationException
     */
    public function store(StoreWithdrawalRequest $request): RedirectResponse
    {
        $user = $request->user();
        $method = $request->payoutMethod();
        $amountInCents = $request->amountInCents();

        Cache::lock("withdrawals:{$user->id}", 10)->block(5, function () use ($request, $user, $method, $amountInCents): void {
            if ($amountInCents > $user->availableBalanceInCents()) {
                throw ValidationException::withMessages([
                    'amount' => 'The amount is more than your available balance.',
                ]);
            }

            $user->transactions()->create([
                'type' => TransactionType::Withdrawal,
                'status' => TransactionStatus::Pending,
                'amount_in_cents' => $amountInCents,
                'method' => $method->label(),
                'destination' => $request->validated('destination'),
            ]);
        });

        $amount = Number::currency($amountInCents / 100);

        return redirect()
            ->route('withdraw')
            ->with('status', "Your withdrawal of {$amount} to {$method->label()} is waiting for review.");
    }
}
