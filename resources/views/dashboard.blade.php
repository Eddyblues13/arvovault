@use('Illuminate\Support\Number')

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
        <a href="{{ route('deposit') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <x-icon name="circle-plus" class="size-4" />
            Add Funds
        </a>
        <a href="{{ route('withdraw') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 ring-1 ring-blue-600/20 transition hover:bg-blue-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-blue-400/10 dark:text-blue-300 dark:ring-blue-400/30 dark:hover:bg-blue-400/20">
            <x-icon name="withdraw" class="size-4" />
            Withdraw
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

    <div class="mt-8 grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
        {{-- Recent activity --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2 dark:border-white/10 dark:bg-slate-900" aria-labelledby="recent-activity-heading">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                <h2 id="recent-activity-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Recent Activity</h2>
                <a href="{{ route('wallet') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
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
                        <x-transaction-item :transaction="$transaction" />
                    @endforeach
                </ul>
            @endif
        </section>

        <div class="grid grid-cols-1 items-start gap-6 sm:grid-cols-2 xl:grid-cols-1">
            {{-- Next booking --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="next-booking-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="next-booking-heading" class="text-base font-semibold text-slate-900 dark:text-white">Next Booking</h2>
                    <a href="{{ route('bookings') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">{{ $nextBooking ? 'View all' : 'Book a session' }}</a>
                </div>

                @if ($nextBooking)
                    <div class="mt-4 flex items-start gap-4">
                        <div class="flex w-14 shrink-0 flex-col items-center rounded-xl bg-blue-50 py-2 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300">
                            <span class="text-[11px] font-semibold uppercase">{{ $nextBooking->scheduled_for->format('M') }}</span>
                            <span class="text-xl leading-none font-bold">{{ $nextBooking->scheduled_for->format('j') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $nextBooking->topic }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $nextBooking->scheduled_for->format('l · g:i A') }} UTC</p>
                            <x-booking-status-badge :status="$nextBooking->status" class="mt-2 inline-block" />
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">No upcoming bookings. Book a session whenever you'd like help from our team.</p>
                @endif
            </section>

            {{-- Latest tips --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="latest-tips-heading">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="latest-tips-heading" class="text-base font-semibold text-slate-900 dark:text-white">Latest Tips</h2>
                    <a href="{{ route('tips') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
                </div>

                @if ($latestTips->isEmpty())
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">New guides from our team will appear here.</p>
                @else
                    <ul class="mt-2 divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($latestTips as $tip)
                            <li>
                                <a href="{{ route('tips.show', $tip) }}" class="group flex items-start gap-3 py-3 focus-visible:outline-2 focus-visible:outline-blue-600">
                                    <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                                        <x-icon name="lightbulb" class="size-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-slate-900 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">{{ $tip->title }}</span>
                                        <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $tip->published_at->format('M j, Y') }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</x-layouts.app>
