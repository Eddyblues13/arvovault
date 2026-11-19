@use('Illuminate\Support\Number')

@props(['transaction'])

<li class="flex items-center gap-3 px-5 py-4 sm:gap-4 sm:px-6">
    <span @class([
        'flex size-10 shrink-0 items-center justify-center rounded-full',
        'bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400' => $transaction->isCredit(),
        'bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400' => ! $transaction->isCredit(),
    ])>
        <x-icon :name="$transaction->isCredit() ? 'arrow-down-left' : 'arrow-up-right'" class="size-4" />
    </span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $transaction->title() }}</p>
        <p class="truncate text-xs text-slate-500 dark:text-slate-400">
            {{ $transaction->created_at->format('M j, Y · g:i A') }}
            @if ($transaction->destination)
                · {{ $transaction->destination }}
            @endif
        </p>
    </div>
    <div class="flex flex-col items-end gap-1">
        <span @class([
            'text-sm font-semibold whitespace-nowrap tabular-nums',
            'text-emerald-600 dark:text-emerald-400' => $transaction->isCredit(),
            'text-slate-900 dark:text-white' => ! $transaction->isCredit(),
        ])>{{ $transaction->isCredit() ? '+' : '−' }}{{ Number::currency($transaction->amount_in_cents / 100) }}</span>
        <x-transaction-status-badge :status="$transaction->status" />
    </div>
</li>
