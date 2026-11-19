@use('App\TransactionStatus')
@use('Illuminate\Support\Number')

<x-layouts.admin title="Dashboard">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Dashboard</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">An overview of accounts, balances and wallet activity.</p>
    </div>

    @if ($pendingTransactionsCount > 0)
        <div class="mt-6 flex flex-col gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5 dark:border-amber-400/20 dark:bg-amber-400/10">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300">
                    <x-icon name="clock" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">{{ trans_choice('{1} :count transaction is waiting for review|[2,*] :count transactions are waiting for review', $pendingTransactionsCount) }}</p>
                    <p class="text-xs text-amber-800/80 dark:text-amber-200/70">Approve or decline pending credits and withdrawals.</p>
                </div>
            </div>
            <a href="{{ route('admin.transactions.index', ['status' => TransactionStatus::Pending->value]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">
                Review now
                <x-icon name="arrow-right" class="size-4" />
            </a>
        </div>
    @endif

    {{-- Platform totals --}}
    <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-6">
        <div class="relative col-span-2 min-w-0 overflow-hidden rounded-2xl bg-linear-to-br from-blue-600 to-indigo-600 p-5 text-white shadow-lg shadow-blue-600/20 sm:p-6 lg:col-span-1">
            <div class="absolute -top-16 -right-16 size-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="relative flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-blue-100">Balances Held</p>
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 ring-inset">
                    <x-icon name="wallet" />
                </span>
            </div>
            <p class="relative mt-3 text-3xl font-bold tracking-tight wrap-break-word tabular-nums lg:text-2xl xl:text-3xl">{{ Number::currency($balancesHeldInCents / 100) }}</p>
            <p class="relative mt-2 text-xs text-blue-100">Available across all user wallets</p>
        </div>

        <x-stat-card label="Users" :value="Number::format($totalUsers)" hint="Registered accounts" icon="users" tone="blue" />

        <x-stat-card label="Total Credited" :value="Number::currency($totalCreditedInCents / 100)" hint="All-time completed credits" icon="arrow-down-left" tone="emerald" />

        <x-stat-card label="Total Withdrawn" :value="Number::currency($totalWithdrawnInCents / 100)" hint="Completed payouts" icon="arrow-up-right" class="col-span-2 lg:col-span-1" />
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-5">
        {{-- Recent transactions --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-3 dark:border-white/10 dark:bg-slate-900" aria-labelledby="recent-transactions-heading">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                <h2 id="recent-transactions-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Recent Transactions</h2>
                <a href="{{ route('admin.transactions.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
            </div>

            @if ($recentTransactions->isEmpty())
                <div class="flex flex-col items-center px-6 py-16 text-center">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <x-icon name="inbox" class="size-7" />
                    </span>
                    <p class="mt-4 font-semibold text-slate-900 dark:text-white">No transactions yet</p>
                    <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Credits and withdrawals from every user will show up here.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($recentTransactions as $transaction)
                        <x-admin.transaction-item :transaction="$transaction" />
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Newest users --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-white/10 dark:bg-slate-900" aria-labelledby="newest-users-heading">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                <h2 id="newest-users-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Newest Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
            </div>

            @if ($recentUsers->isEmpty())
                <div class="flex flex-col items-center px-6 py-16 text-center">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <x-icon name="users" class="size-7" />
                    </span>
                    <p class="mt-4 font-semibold text-slate-900 dark:text-white">No users yet</p>
                    <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">New sign-ups will appear here.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($recentUsers as $user)
                        <li>
                            <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3 px-5 py-4 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 sm:px-6 dark:hover:bg-white/5">
                                <x-admin.user-avatar :user="$user" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold whitespace-nowrap text-slate-900 tabular-nums dark:text-white">{{ Number::currency($user->available_balance_in_cents / 100) }}</p>
                                    <p class="text-xs whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.admin>
