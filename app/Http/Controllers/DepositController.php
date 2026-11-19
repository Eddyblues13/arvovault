<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositRequest;
use App\Models\DepositAccount;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\View\View;

class DepositController extends Controller
{
    /**
     * Show where to send a deposit and the user's recent deposit requests.
     */
    public function create(Request $request): View
    {
        return view('deposit', [
            'depositAccounts' => DepositAccount::query()->available()->orderBy('id')->get(),
            'deposits' => $request->user()->transactions()->credits()->whereNotNull('method')->latest()->latest('id')->limit(10)->get(),
        ]);
    }

    /**
     * Record a deposit the user has sent, as a pending credit that an admin confirms.
     */
    public function store(StoreDepositRequest $request): RedirectResponse
    {
        $method = $request->payoutMethod();
        $amountInCents = $request->amountInCents();

        $request->user()->transactions()->create([
            'type' => TransactionType::Credit,
            'status' => TransactionStatus::Pending,
            'amount_in_cents' => $amountInCents,
            'method' => $method->label(),
            'reference' => $request->validated('reference'),
        ]);

        $amount = Number::currency($amountInCents / 100);

        return redirect()
            ->route('deposit')
            ->with('status', "Your deposit of {$amount} via {$method->label()} was submitted. It's added to your balance once our team confirms it.");
    }
}
