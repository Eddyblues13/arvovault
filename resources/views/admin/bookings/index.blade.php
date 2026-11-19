@use('App\BookingStatus')

@php
    $statusFilters = [
        ['value' => null, 'label' => 'All bookings'],
        ...array_map(fn (BookingStatus $case): array => ['value' => $case, 'label' => $case->label()], BookingStatus::cases()),
    ];
@endphp

<x-layouts.admin title="Bookings">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Bookings</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Confirm or cancel the sessions users request.</p>
    </div>

    <nav aria-label="Filter by status" class="-mx-4 mt-6 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
        <ul class="flex w-max gap-2">
            @foreach ($statusFilters as $filter)
                @php($isActive = $status === $filter['value'])
                <li>
                    <a
                        href="{{ route('admin.bookings.index', array_filter(['status' => $filter['value']?->value])) }}"
                        @if ($isActive) aria-current="true" @endif
                        @class([
                            'inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-sm font-medium whitespace-nowrap transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600',
                            'bg-slate-900 text-white dark:bg-white dark:text-slate-900' => $isActive,
                            'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-slate-900 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:text-white' => ! $isActive,
                        ])
                    >
                        {{ $filter['label'] }}
                        @if ($filter['value'] === BookingStatus::Pending && $pendingCount > 0)
                            <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[11px] leading-none font-semibold text-white tabular-nums">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Bookings">
        @if ($bookings->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="calendar" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No bookings found</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">{{ $status ? 'Nothing matches this filter right now.' : 'Bookings that users request will show up here.' }}</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($bookings as $booking)
                    <li class="@container">
                        <div class="flex flex-wrap items-start gap-4 px-5 py-4 sm:px-6 @2xl:flex-nowrap">
                            <div @class([
                                'flex w-14 shrink-0 flex-col items-center rounded-xl py-2',
                                'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300' => $booking->scheduled_for->isFuture(),
                                'bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400' => $booking->scheduled_for->isPast(),
                            ])>
                                <span class="text-[11px] font-semibold uppercase">{{ $booking->scheduled_for->format('M') }}</span>
                                <span class="text-xl leading-none font-bold">{{ $booking->scheduled_for->format('j') }}</span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->topic }}</p>
                                    <x-booking-status-badge :status="$booking->status" />
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    <a href="{{ route('admin.users.show', $booking->user) }}" class="font-medium text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400">{{ $booking->user->name }}</a>
                                    · {{ $booking->scheduled_for->format('D, M j, Y · g:i A') }} UTC
                                </p>
                                @if ($booking->notes)
                                    <p class="mt-2 text-sm whitespace-pre-line text-slate-600 dark:text-slate-400">{{ $booking->notes }}</p>
                                @endif
                            </div>

                            @if ($booking->isCancellable())
                                <div class="flex w-full gap-2 pl-18 @2xl:w-auto @2xl:pl-0">
                                    @if ($booking->isPending())
                                        <form method="POST" action="{{ route('admin.bookings.status.update', $booking) }}" class="flex-1 @2xl:flex-none">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ BookingStatus::Confirmed->value }}">
                                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                                                <x-icon name="check" class="size-3.5" />
                                                Confirm<span class="sr-only">: {{ $booking->topic }}</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.bookings.status.update', $booking) }}" data-confirm="Cancel this booking? The user will be notified." class="flex-1 @2xl:flex-none">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ BookingStatus::Cancelled->value }}">
                                        <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-red-200 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-white/5 dark:text-red-400 dark:ring-red-400/30 dark:hover:bg-red-400/10">
                                            <x-icon name="x" class="size-3.5" />
                                            Cancel<span class="sr-only">: {{ $booking->topic }}</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($bookings->hasPages())
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</x-layouts.admin>
