@use('App\Http\Requests\Admin\FundUserRequest')
@use('Illuminate\Support\Number')

<x-layouts.admin :title="$user->name">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All users
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <x-admin.user-avatar :user="$user" class="size-14 text-lg" />
            <div class="min-w-0">
                <h1 class="truncate text-2xl font-bold tracking-tight sm:text-3xl">{{ $user->name }}</h1>
                <p class="mt-0.5 truncate text-sm text-slate-600 dark:text-slate-400">{{ $user->email }} · Joined {{ $user->created_at->format('M j, Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:flex">
            <a href="{{ route('admin.support.show', $user) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
                <x-icon name="chat" class="size-4" />
                Message
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
                <x-icon name="pencil" class="size-4" />
                Edit details
            </a>

            @if ($user->isSuspended())
                <form method="POST" action="{{ route('admin.users.suspension.destroy', $user) }}" class="col-span-2 sm:col-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                        <x-icon name="check" class="size-4" />
                        Reactivate
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.suspension.store', $user) }}" data-confirm="Suspend {{ $user->name }}? They'll be signed out and can't sign back in until you reactivate the account." class="col-span-2 sm:col-auto">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-red-200 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-white/5 dark:text-red-400 dark:ring-red-400/30 dark:hover:bg-red-400/10">
                        <x-icon name="ban" class="size-4" />
                        Suspend
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($user->isSuspended())
        <div role="status" class="mt-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-200">
            <x-icon name="ban" class="mt-0.5 size-4" />
            <p><span class="font-semibold">Suspended since {{ $user->suspended_at->format('M j, Y') }}.</span> {{ $user->name }} can't sign in or use their wallet until you reactivate the account.</p>
        </div>
    @endif

    {{-- Balances --}}
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-6">
        <div class="relative col-span-2 min-w-0 overflow-hidden rounded-2xl bg-linear-to-br from-blue-600 to-indigo-600 p-5 text-white shadow-lg shadow-blue-600/20 sm:p-6 md:col-span-1">
            <div class="absolute -top-16 -right-16 size-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="relative flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-blue-100">Available Balance</p>
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 ring-inset">
                    <x-icon name="wallet" />
                </span>
            </div>
            <p class="relative mt-3 text-3xl font-bold tracking-tight wrap-break-word tabular-nums md:text-2xl xl:text-3xl">{{ Number::currency($availableBalanceInCents / 100) }}</p>
            <p class="relative mt-2 text-xs text-blue-100">After pending withdrawals</p>
        </div>

        <x-stat-card label="Total Credited" :value="Number::currency($totalCreditedInCents / 100)" hint="All-time completed credits" icon="arrow-down-left" tone="emerald" />

        <x-stat-card label="Total Withdrawn" :value="Number::currency($totalWithdrawnInCents / 100)" hint="Completed payouts" icon="arrow-up-right" />
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        {{-- Fund account --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-start-3 lg:row-start-1 dark:border-white/10 dark:bg-slate-900" aria-labelledby="fund-account-heading">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 ring-inset dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                    <x-icon name="banknote" />
                </span>
                <div>
                    <h2 id="fund-account-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Fund account</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Adds a completed credit to this balance.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.funds.store', $user) }}" data-confirm="Add these funds to {{ $user->name }}'s balance?" class="mt-5 space-y-4">
                @csrf

                <x-input-field label="Amount (USD)" name="amount" type="number" inputmode="decimal" step="0.01" min="0.01" :max="FundUserRequest::MAX_AMOUNT" placeholder="0.00" required />

                <x-input-field label="Description" name="description" maxlength="255" placeholder="e.g. Welcome bonus" hint="Optional. Shown to the user in their activity." />

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <x-icon name="plus" class="size-4" />
                    Add funds
                </button>
            </form>
        </section>

        {{-- Transactions --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 lg:row-start-1 dark:border-white/10 dark:bg-slate-900" aria-labelledby="transactions-heading">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                <h2 id="transactions-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Transactions</h2>
            </div>

            @if ($transactions->isEmpty())
                <div class="flex flex-col items-center px-6 py-16 text-center">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <x-icon name="inbox" class="size-7" />
                    </span>
                    <p class="mt-4 font-semibold text-slate-900 dark:text-white">No transactions yet</p>
                    <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Funds you add and withdrawals the user requests will show up here.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($transactions as $transaction)
                        <x-admin.transaction-item :transaction="$transaction" :show-user="false" />
                    @endforeach
                </ul>

                @if ($transactions->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>
</x-layouts.admin>
