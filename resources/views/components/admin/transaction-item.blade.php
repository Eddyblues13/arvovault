@use('App\TransactionStatus')
@use('Illuminate\Support\Number')
@use('Illuminate\Support\Str')

@props(['transaction', 'showUser' => true])

@php
    $amount = Number::currency($transaction->amount_in_cents / 100);
    $typeLabel = Str::lower($transaction->type->label());
@endphp

{{-- The row is a size container, so the actions move to their own line whenever the list is narrow. --}}
<li class="@container">
    <div class="flex flex-wrap items-center gap-3 px-5 py-4 sm:px-6 @xl:flex-nowrap @xl:gap-4">
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
                @if ($showUser)
                    <a href="{{ route('admin.users.show', $transaction->user) }}" class="font-medium text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">{{ $transaction->user->name }}</a>
                    ·
                @endif
                {{ $transaction->created_at->format('M j, Y · g:i A') }}
                @if ($transaction->admin)
                    · Added by {{ $transaction->admin->name }}
                @endif
            </p>
            @if ($transaction->destination)
                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">Send to <span class="font-medium text-slate-700 select-all dark:text-slate-200">{{ $transaction->destination }}</span></p>
            @endif
        </div>

        <div class="flex flex-col items-end gap-1">
            <span @class([
                'text-sm font-semibold whitespace-nowrap tabular-nums',
                'text-emerald-600 dark:text-emerald-400' => $transaction->isCredit(),
                'text-slate-900 dark:text-white' => ! $transaction->isCredit(),
            ])>{{ $transaction->isCredit() ? '+' : '−' }}{{ $amount }}</span>
            <x-transaction-status-badge :status="$transaction->status" />
        </div>

        @if ($transaction->isPending())
            <div class="flex w-full gap-2 pl-13 @xl:w-auto @xl:pl-0">
                <form method="POST" action="{{ route('admin.transactions.status.update', $transaction) }}" data-confirm="Approve this {{ $typeLabel }} of {{ $amount }}?" class="flex-1 @xl:flex-none">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ TransactionStatus::Completed->value }}">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                        <x-icon name="check" class="size-3.5" />
                        Approve<span class="sr-only">: {{ $transaction->title() }}, {{ $amount }}</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.transactions.status.update', $transaction) }}" data-confirm="Decline this {{ $typeLabel }} of {{ $amount }}?" class="flex-1 @xl:flex-none">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ TransactionStatus::Failed->value }}">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-red-200 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-white/5 dark:text-red-400 dark:ring-red-400/30 dark:hover:bg-red-400/10">
                        <x-icon name="x" class="size-3.5" />
                        Decline<span class="sr-only">: {{ $transaction->title() }}, {{ $amount }}</span>
                    </button>
                </form>
            </div>
        @endif
    </div>
</li>
