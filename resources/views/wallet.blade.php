@use('App\TransactionType')
@use('Illuminate\Support\Number')

@php
    $typeFilters = [
        ['value' => null, 'label' => 'All'],
        ['value' => TransactionType::Credit, 'label' => 'Credits'],
        ['value' => TransactionType::Withdrawal, 'label' => 'Withdrawals'],
    ];
@endphp

<x-layouts.app title="Wallet">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Wallet</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Your balance breakdown and full transaction history.</p>
    </div>

    {{-- Balance breakdown --}}
    <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
        <div class="relative flex min-w-0 flex-col justify-between overflow-hidden rounded-2xl bg-linear-to-br from-blue-600 to-indigo-600 p-5 text-white shadow-lg shadow-blue-600/20 sm:p-6">
            <div class="absolute -top-16 -right-16 size-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-blue-100">Available Balance</p>
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 ring-inset">
                        <x-icon name="wallet" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight wrap-break-word tabular-nums sm:text-4xl">{{ Number::currency($availableBalanceInCents / 100) }}</p>
                <p class="mt-2 text-xs text-blue-100">Credited funds minus completed and pending withdrawals</p>
            </div>
            <a href="{{ route('withdraw') }}" class="relative mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                <x-icon name="withdraw" class="size-4" />
                Withdraw Funds
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:col-span-2 lg:gap-6">
            <x-stat-card label="Total Credited" :value="Number::currency($totalCreditedInCents / 100)" hint="All-time completed credits" icon="arrow-down-left" tone="emerald" />
            <x-stat-card label="Total Withdrawn" :value="Number::currency($totalWithdrawnInCents / 100)" hint="Completed payouts" icon="arrow-up-right" />
            <x-stat-card label="Pending Withdrawals" :value="Number::currency($pendingWithdrawalsInCents / 100)" hint="Reserved until our team reviews them" icon="clock" />
            <x-stat-card label="Pending Credits" :value="Number::currency($pendingCreditsInCents / 100)" hint="Added once they are approved" icon="clock" tone="blue" />
        </div>
    </div>

    {{-- Transaction history --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-labelledby="history-heading">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-white/5">
            <h2 id="history-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Transaction History</h2>

            <nav aria-label="Filter by type">
                <ul class="flex gap-2">
                    @foreach ($typeFilters as $filter)
                        @php($isActive = $type === $filter['value'])
                        <li>
                            <a
                                href="{{ route('wallet', array_filter(['type' => $filter['value']?->value])) }}"
                                @if ($isActive) aria-current="true" @endif
                                @class([
                                    'inline-flex rounded-full px-3.5 py-1.5 text-sm font-medium whitespace-nowrap transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600',
                                    'bg-slate-900 text-white dark:bg-white dark:text-slate-900' => $isActive,
                                    'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-900 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:text-white' => ! $isActive,
                                ])
                            >{{ $filter['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>

        @if ($transactions->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="inbox" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No transactions yet</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">
                    {{ $type ? 'Nothing matches this filter yet.' : 'Your credits and withdrawals will show up here as soon as they happen.' }}
                </p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($transactions as $transaction)
                    <x-transaction-item :transaction="$transaction" />
                @endforeach
            </ul>

            @if ($transactions->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.app>
