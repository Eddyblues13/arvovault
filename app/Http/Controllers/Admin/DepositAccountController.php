<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDepositAccountsRequest;
use App\Models\DepositAccount;
use App\PayoutMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepositAccountController extends Controller
{
    /**
     * Show where users send deposits for each payment method.
     */
    public function edit(): View
    {
        return view('admin.deposit-accounts.edit', [
            'payoutMethods' => PayoutMethod::cases(),
            'accounts' => DepositAccount::query()->get()->keyBy(fn (DepositAccount $account): string => $account->method->value),
        ]);
    }

    /**
     * Save the deposit details for every payment method.
     */
    public function update(UpdateDepositAccountsRequest $request): RedirectResponse
    {
        foreach (PayoutMethod::cases() as $method) {
            $fields = $request->validated("accounts.{$method->value}", []);

            DepositAccount::updateOrCreate(['method' => $method], [
                'account_details' => $fields['account_details'] ?? null,
                'instructions' => $fields['instructions'] ?? null,
                'is_active' => (bool) ($fields['is_active'] ?? false),
            ]);
        }

        return redirect()->route('admin.deposit-accounts.edit')->with('status', 'Deposit methods saved.');
    }
}
