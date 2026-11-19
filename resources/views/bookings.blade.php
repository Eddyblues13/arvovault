@use('App\Http\Requests\StoreBookingRequest')

<x-layouts.app title="Bookings">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Bookings</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Book a session with our team and keep track of your bookings.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-5">
        {{-- Request a booking --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2 dark:border-white/10 dark:bg-slate-900" aria-labelledby="request-booking-heading">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                    <x-icon name="calendar" />
                </span>
                <div>
                    <h2 id="request-booking-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Request a booking</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">We'll confirm it or suggest another time.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('bookings.store') }}" class="mt-5 space-y-4">
                @csrf

                <x-input-field label="Date and time (UTC)" name="scheduled_for" type="datetime-local" :min="now()->addHour()->format('Y-m-d\TH:i')" :max="now()->addMonths(StoreBookingRequest::MAX_MONTHS_AHEAD)->format('Y-m-d\TH:i')" hint="All times are in Coordinated Universal Time (UTC)." required />

                <x-input-field label="Topic" name="topic" maxlength="120" placeholder="e.g. Help with a payout" required />

                <x-textarea-field label="Notes" name="notes" rows="3" maxlength="1000" placeholder="Anything we should know before the session?" hint="Optional." />

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <x-icon name="calendar" class="size-4" />
                    Request booking
                </button>
            </form>
        </section>

        <div class="space-y-6 lg:col-span-3">
            {{-- Upcoming --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-labelledby="upcoming-heading">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                    <h2 id="upcoming-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Upcoming</h2>
                </div>

                @if ($upcomingBookings->isEmpty())
                    <div class="flex flex-col items-center px-6 py-12 text-center">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                            <x-icon name="calendar" class="size-6" />
                        </span>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">No upcoming bookings</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Bookings you request will show up here.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($upcomingBookings as $booking)
                            <li class="flex items-start gap-4 px-5 py-4 sm:px-6">
                                <div class="flex w-14 shrink-0 flex-col items-center rounded-xl bg-blue-50 py-2 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300">
                                    <span class="text-[11px] font-semibold uppercase">{{ $booking->scheduled_for->format('M') }}</span>
                                    <span class="text-xl leading-none font-bold">{{ $booking->scheduled_for->format('j') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->topic }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $booking->scheduled_for->format('l, M j, Y · g:i A') }} UTC</p>
                                    @if ($booking->notes)
                                        <p class="mt-1 text-sm whitespace-pre-line text-slate-600 dark:text-slate-400">{{ $booking->notes }}</p>
                                    @endif
                                </div>
                                <x-booking-status-badge :status="$booking->status" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Past --}}
            @if ($pastBookings->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-labelledby="past-heading">
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                        <h2 id="past-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Past</h2>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($pastBookings as $booking)
                            <li class="flex items-center gap-4 px-5 py-4 sm:px-6">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-700 dark:text-slate-300">{{ $booking->topic }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $booking->scheduled_for->format('M j, Y · g:i A') }} UTC</p>
                                </div>
                                <x-booking-status-badge :status="$booking->status" />
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>
    </div>
</x-layouts.app>
