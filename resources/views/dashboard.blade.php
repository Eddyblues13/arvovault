@use('Illuminate\Support\Number')
@use('App\TransactionStatus')

<x-layouts.app title="Dashboard">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Welcome back, {{ $firstName }}</p>
    </div>

    {{-- Balance overview --}}
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-6">
        <div class="relative col-span-2 overflow-hidden rounded-2xl bg-linear-to-br from-blue-600 to-indigo-600 p-5 text-white shadow-lg shadow-blue-600/20 sm:p-6 md:col-span-1">
            <div class="absolute -top-16 -right-16 size-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="relative flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-blue-100">Available Balance</p>
                <span class="flex size-10 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 ring-inset">
                    <x-icon name="wallet" />
                </span>
            </div>
            <p class="relative mt-3 text-3xl font-bold tracking-tight tabular-nums sm:text-4xl">{{ Number::currency($availableBalanceInCents / 100) }}</p>
            <p class="relative mt-2 text-xs text-blue-100">Ready to withdraw</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Credited</p>
                <span class="hidden size-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 ring-inset sm:flex dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                    <x-icon name="arrow-down-left" />
                </span>
            </div>
            <p class="mt-3 text-xl font-bold tracking-tight text-emerald-600 tabular-nums sm:text-3xl dark:text-emerald-400">{{ Number::currency($totalCreditedInCents / 100) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">All-time credits</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Withdrawn</p>
                <span class="hidden size-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 ring-1 ring-slate-200 ring-inset sm:flex dark:bg-white/5 dark:text-slate-300 dark:ring-white/10">
                    <x-icon name="arrow-up-right" />
                </span>
            </div>
            <p class="mt-3 text-xl font-bold tracking-tight text-slate-900 tabular-nums sm:text-3xl dark:text-white">{{ Number::currency($totalWithdrawnInCents / 100) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Completed payouts</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="mt-6 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap">
        <a href="{{ route('withdraw') }}" class="col-span-2 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <x-icon name="withdraw" class="size-4" />
            Withdraw Funds
        </a>
        <a href="{{ route('wallet') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
            <x-icon name="wallet" class="size-4" />
            View Wallet
        </a>
        <button type="button" data-open-chat class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
            <x-icon name="chat" class="size-4" />
            Contact Support
        </button>
    </div>

    {{-- Recent activity --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-labelledby="recent-activity-heading">
        <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
            <h2 id="recent-activity-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Recent Activity</h2>
        </div>

        @if ($recentTransactions->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="inbox" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No transactions yet</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Your credits and withdrawals will show up here as soon as they happen.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($recentTransactions as $transaction)
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
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $transaction->created_at->format('M j, Y · g:i A') }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span @class([
                                'text-sm font-semibold tabular-nums',
                                'text-emerald-600 dark:text-emerald-400' => $transaction->isCredit(),
                                'text-slate-900 dark:text-white' => ! $transaction->isCredit(),
                            ])>{{ $transaction->isCredit() ? '+' : '−' }}{{ Number::currency($transaction->amount_in_cents / 100) }}</span>
                            <span @class([
                                'rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset',
                                'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20' => $transaction->status === TransactionStatus::Completed,
                                'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20' => $transaction->status === TransactionStatus::Pending,
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-300 dark:ring-red-400/20' => $transaction->status === TransactionStatus::Failed,
                            ])>{{ $transaction->status->label() }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layouts.app>
