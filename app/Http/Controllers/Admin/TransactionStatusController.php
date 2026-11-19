<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTransactionStatusRequest;
use App\Models\Transaction;
use App\Notifications\TransactionStatusUpdated;
use App\TransactionStatus;
use Illuminate\Http\RedirectResponse;

class TransactionStatusController extends Controller
{
    /**
     * Approve or decline a pending transaction, and tell the user.
     *
     * The pending check is part of the update query itself, so when two admins act on
     * the same transaction at once, only the first change is applied.
     */
    public function update(UpdateTransactionStatusRequest $request, Transaction $transaction): RedirectResponse
    {
        $status = $request->enum('status', TransactionStatus::class);

        $updatedCount = Transaction::query()
            ->whereKey($transaction)
            ->pending()
            ->update(['status' => $status]);

        if ($updatedCount === 0) {
            return back()->with('error', 'This transaction has already been processed.');
        }

        $transaction->refresh();
        $transaction->user->notify(new TransactionStatusUpdated($transaction));

        return back()->with('status', $status === TransactionStatus::Completed ? 'Transaction approved.' : 'Transaction declined.');
    }
}
