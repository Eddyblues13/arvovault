@use('App\TransactionStatus')
@use('App\TransactionType')

@php
    $statusFilters = [
        ['value' => null, 'label' => 'All statuses'],
        ...array_map(fn (TransactionStatus $case): array => ['value' => $case, 'label' => $case->label()], TransactionStatus::cases()),
    ];

    $typeFilters = [
        ['value' => null, 'label' => 'All types'],
        ['value' => TransactionType::Credit, 'label' => 'Credits'],
        ['value' => TransactionType::Withdrawal, 'label' => 'Withdrawals'],
    ];

    $pillClasses = 'inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-medium whitespace-nowrap transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
    $activePillClasses = 'bg-slate-900 text-white dark:bg-white dark:text-slate-900';
    $inactivePillClasses = 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-900 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:text-white';
@endphp

<x-layouts.admin title="Transactions">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Transactions</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Review pending requests and browse wallet activity across all users.</p>
    </div>

    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <nav aria-label="Filter by status" class="-mx-4 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
            <ul class="flex w-max gap-2">
                @foreach ($statusFilters as $filter)
                    @php($isActive = $status === $filter['value'])
                    <li>
                        <a href="{{ route('admin.transactions.index', array_filter(['status' => $filter['value']?->value, 'type' => $type?->value])) }}" @if ($isActive) aria-current="true" @endif class="{{ $pillClasses }} {{ $isActive ? $activePillClasses : $inactivePillClasses }}">
                            {{ $filter['label'] }}
                            @if ($filter['value'] === TransactionStatus::Pending && $pendingCount > 0)
                                <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[11px] leading-none font-semibold text-white tabular-nums">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <nav aria-label="Filter by type" class="-mx-4 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
            <ul class="flex w-max gap-2">
                @foreach ($typeFilters as $filter)
                    @php($isActive = $type === $filter['value'])
                    <li>
                        <a href="{{ route('admin.transactions.index', array_filter(['status' => $status?->value, 'type' => $filter['value']?->value])) }}" @if ($isActive) aria-current="true" @endif class="{{ $pillClasses }} {{ $isActive ? $activePillClasses : $inactivePillClasses }}">
                            {{ $filter['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Transactions">
        @if ($transactions->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="inbox" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No transactions found</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">
                    @if ($status || $type)
                        Nothing matches these filters right now.
                    @else
                        Credits and withdrawals from every user will show up here.
                    @endif
                </p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($transactions as $transaction)
                    <x-admin.transaction-item :transaction="$transaction" />
                @endforeach
            </ul>
        @endif
    </section>

    @if ($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @endif
</x-layouts.admin>
