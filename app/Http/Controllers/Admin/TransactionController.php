<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\TransactionStatus;
use App\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * List every user's transactions newest first, optionally filtered by status and type.
     *
     * Unknown filter values are ignored, so the list falls back to all transactions.
     */
    public function index(Request $request): View
    {
        $status = TransactionStatus::tryFrom($request->string('status')->toString());
        $type = TransactionType::tryFrom($request->string('type')->toString());

        $transactions = Transaction::query()
            ->with(['user', 'admin'])
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($type, fn (Builder $query): Builder => $query->where('type', $type))
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'status' => $status,
            'type' => $type,
            'pendingCount' => Transaction::query()->pending()->count(),
        ]);
    }
}
