@php
    $signInUrl = route('login');
    $getStartedUrl = route('register');

    $activity = [
        ['title' => 'Payout to PayPal', 'time' => 'Today, 9:41 AM', 'amount' => '−$150.00', 'status' => 'Completed', 'incoming' => false, 'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20'],
        ['title' => 'Earnings deposit', 'time' => 'Yesterday, 4:12 PM', 'amount' => '+$420.00', 'status' => 'Received', 'incoming' => true, 'badge' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-300 dark:ring-blue-400/20'],
        ['title' => 'Payout to Crypto (USDT)', 'time' => 'Nov 14, 11:05 AM', 'amount' => '−$75.00', 'status' => 'Pending', 'incoming' => false, 'badge' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20'],
        ['title' => 'Payout to Zelle', 'time' => 'Nov 12, 2:30 PM', 'amount' => '−$200.00', 'status' => 'Completed', 'incoming' => false, 'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20'],
    ];

    $chartBars = [38, 52, 34, 61, 47, 72, 58, 84, 66, 100];

    $steps = [
        ['title' => 'Create your account', 'body' => 'Sign up in minutes and get instant access to your secure wallet.'],
        ['title' => 'Track your balance', 'body' => 'Watch your earnings and transactions update in real time.'],
        ['title' => 'Request a payout', 'body' => 'Choose Cash App, Zelle, PayPal, crypto, or another method and withdraw in a few clicks.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <meta name="description" content="Manage your balance, request withdrawals, and track your transactions all in one place. Fast, secure, and simple.">

        <title>{{ config('app.name', 'Laravel') }} — Secure Wallet &amp; Payout Platform</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        {{-- Header --}}
        <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/75 backdrop-blur-lg dark:border-white/10 dark:bg-slate-950/70">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-18 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-600">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-600/25 ring-1 ring-white/20 ring-inset">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex dark:text-slate-400" aria-label="Primary">
                    <a href="#features" class="transition hover:text-slate-900 dark:hover:text-white">Features</a>
                    <a href="#how-it-works" class="transition hover:text-slate-900 dark:hover:text-white">How it works</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ $signInUrl }}" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:ring-0 dark:text-slate-300 dark:ring-white/15 dark:hover:bg-white/10 dark:hover:text-white">
                            Sign In
                        </a>
                        <a href="{{ $getStartedUrl }}" class="hidden items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:inline-flex">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="relative isolate overflow-hidden">
                <div class="hero-grid absolute inset-0 -z-10" aria-hidden="true"></div>
                <div class="absolute top-0 left-1/2 -z-10 h-120 w-225 max-w-none -translate-x-1/2 -translate-y-1/3 rounded-full bg-linear-to-b from-blue-400/30 via-indigo-400/10 to-transparent blur-3xl dark:from-blue-500/20" aria-hidden="true"></div>

                <div class="mx-auto max-w-6xl px-4 pt-16 pb-20 sm:px-6 sm:pt-24 sm:pb-24 lg:px-8 lg:pt-28">
                    <div class="text-center transition duration-700 ease-out starting:translate-y-4 starting:opacity-0 motion-reduce:transition-none">
                        <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/80 px-3.5 py-1.5 text-xs font-semibold text-blue-700 shadow-sm backdrop-blur sm:text-sm dark:border-blue-400/20 dark:bg-blue-400/10 dark:text-blue-300">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                            Fast, secure &amp; simple payouts
                        </span>

                        <h1 class="mx-auto mt-6 max-w-4xl text-4xl leading-[1.08] font-bold tracking-tight text-balance text-slate-900 sm:text-5xl md:text-6xl lg:text-7xl dark:text-white">
                            Your Secure
                            <span class="bg-linear-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent dark:from-blue-400 dark:to-indigo-300">Wallet &amp; Payout</span>
                            Platform
                        </h1>

                        <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-pretty text-slate-600 sm:text-lg dark:text-slate-400">
                            Manage your balance, request withdrawals, and track your transactions all in one place. Fast, secure, and simple.
                        </p>

                        <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <a href="{{ $getStartedUrl }}" class="group inline-flex w-full max-w-xs items-center justify-center gap-2 rounded-xl bg-blue-600 px-7 py-3.5 text-base font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 hover:shadow-blue-600/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto">
                                Get Started
                                <svg class="size-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                            <a href="#how-it-works" class="inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-white px-7 py-3.5 text-base font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto dark:bg-white/5 dark:text-slate-200 dark:ring-white/15 dark:hover:bg-white/10">
                                See how it works
                            </a>
                        </div>

                        <div class="mt-10 flex flex-col items-center gap-3">
                            <p class="text-xs font-semibold tracking-wider text-slate-500 uppercase">Withdraw to</p>
                            <ul class="flex flex-wrap items-center justify-center gap-2">
                                @foreach (['Cash App', 'Zelle', 'PayPal', 'Crypto', 'and more'] as $payoutMethod)
                                    <li class="rounded-full border border-slate-200 bg-white px-3.5 py-1 text-sm font-medium text-slate-700 shadow-xs dark:border-white/10 dark:bg-white/5 dark:text-slate-300">{{ $payoutMethod }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Dashboard preview --}}
                    <div class="relative mx-auto mt-16 max-w-5xl transition delay-150 duration-1000 ease-out starting:translate-y-8 starting:opacity-0 motion-reduce:transition-none sm:mt-20" role="img" aria-label="Preview of the {{ config('app.name', 'Laravel') }} dashboard showing an available balance and recent payouts">
                        <div class="rounded-2xl border border-slate-200 bg-white/60 p-2 shadow-2xl shadow-slate-900/10 backdrop-blur sm:rounded-3xl sm:p-3 dark:border-white/10 dark:bg-white/5 dark:shadow-black/40">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white text-left sm:rounded-2xl dark:border-white/10 dark:bg-slate-900">
                                <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 dark:border-white/5">
                                    <span class="size-2.5 rounded-full bg-slate-200 dark:bg-white/15"></span>
                                    <span class="size-2.5 rounded-full bg-slate-200 dark:bg-white/15"></span>
                                    <span class="size-2.5 rounded-full bg-slate-200 dark:bg-white/15"></span>
                                    <span class="ml-3 text-xs font-medium text-slate-400 dark:text-slate-500">Dashboard</span>
                                </div>

                                <div class="grid grid-cols-1 gap-4 p-4 sm:gap-5 sm:p-6 lg:grid-cols-5">
                                    {{-- Balance card --}}
                                    <div class="relative flex flex-col overflow-hidden rounded-xl bg-linear-to-br from-blue-600 to-indigo-600 p-5 text-white shadow-lg shadow-blue-600/20 sm:p-6 lg:col-span-2">
                                        <div class="absolute -top-16 -right-16 size-48 rounded-full bg-white/10 blur-2xl"></div>
                                        <div class="relative flex items-center justify-between">
                                            <span class="text-sm font-medium text-blue-100">Available balance</span>
                                            <svg class="size-5 text-blue-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                                        </div>
                                        <p class="relative mt-2 text-3xl font-bold tracking-tight sm:text-4xl">$4,250.80</p>
                                        <span class="relative mt-3 inline-flex w-fit items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-1 text-xs font-medium">
                                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                                            +$320.00 this week
                                        </span>

                                        <div class="relative mt-6 flex h-16 items-end gap-1.5">
                                            @foreach ($chartBars as $barHeight)
                                                <span class="flex-1 rounded-sm {{ $loop->last ? 'bg-white' : 'bg-white/30' }}" style="height: {{ $barHeight }}%"></span>
                                            @endforeach
                                        </div>

                                        <div class="relative mt-6 grid grid-cols-2 gap-2 text-sm font-semibold">
                                            <span class="rounded-lg bg-white py-2.5 text-center text-blue-700">Withdraw</span>
                                            <span class="rounded-lg bg-white/15 py-2.5 text-center text-white ring-1 ring-white/20 ring-inset">Add funds</span>
                                        </div>
                                    </div>

                                    {{-- Recent activity --}}
                                    <div class="rounded-xl border border-slate-200 p-5 sm:p-6 lg:col-span-3 dark:border-white/10">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-900 dark:text-white">Recent activity</span>
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">View all</span>
                                        </div>
                                        <ul class="mt-4 divide-y divide-slate-100 dark:divide-white/5">
                                            @foreach ($activity as $item)
                                                <li class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 sm:gap-4">
                                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full {{ $item['incoming'] ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400' }}">
                                                        @if ($item['incoming'])
                                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 7 7 17"/><path d="M17 17H7V7"/></svg>
                                                        @else
                                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                                                        @endif
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $item['time'] }}</p>
                                                    </div>
                                                    <div class="flex flex-col items-end gap-1">
                                                        <span class="text-sm font-semibold tabular-nums {{ $item['incoming'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }}">{{ $item['amount'] }}</span>
                                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $item['badge'] }}">{{ $item['status'] }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Floating confirmation toast --}}
                        <div class="absolute bottom-10 -left-6 hidden items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-xl shadow-slate-900/10 lg:flex dark:border-white/10 dark:bg-slate-800">
                            <span class="flex size-9 items-center justify-center rounded-full bg-emerald-500 text-white">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Withdrawal sent</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">$150.00 to PayPal</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section id="features" class="scroll-mt-20 border-y border-slate-200/70 bg-white py-20 sm:py-28 dark:border-white/10 dark:bg-slate-900/40">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">Everything in one place</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-balance sm:text-4xl">Built for fast, stress-free payouts</h2>
                        <p class="mt-4 text-base leading-relaxed text-pretty text-slate-600 sm:text-lg dark:text-slate-400">From tracking every dollar to getting paid, we keep the whole process simple.</p>
                    </div>

                    <div class="mt-14 grid gap-6 md:grid-cols-3 lg:gap-8">
                        {{-- Wallet Management --}}
                        <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5 lg:p-8 dark:border-white/10 dark:bg-slate-900 dark:hover:border-blue-400/30">
                            <span class="flex size-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                            </span>
                            <h3 class="mt-6 text-lg font-semibold text-slate-900 dark:text-white">Wallet Management</h3>
                            <p class="mt-2 leading-relaxed text-slate-600 dark:text-slate-400">Track your balance, view transactions, and monitor your earnings in real time.</p>
                            <div class="mt-auto pt-6">
                                <ul class="space-y-2.5 border-t border-slate-100 pt-6 text-sm text-slate-600 dark:border-white/5 dark:text-slate-400">
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Live balance updates</li>
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Full transaction history</li>
                                </ul>
                            </div>
                        </article>

                        {{-- Easy Withdrawals --}}
                        <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-900/5 lg:p-8 dark:border-white/10 dark:bg-slate-900 dark:hover:border-emerald-400/30">
                            <span class="flex size-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 ring-inset dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 17V3"/><path d="m6 11 6 6 6-6"/><path d="M19 21H5"/></svg>
                            </span>
                            <h3 class="mt-6 text-lg font-semibold text-slate-900 dark:text-white">Easy Withdrawals</h3>
                            <p class="mt-2 leading-relaxed text-slate-600 dark:text-slate-400">Request payouts to Cash App, Zelle, PayPal, crypto, and more with just a few clicks.</p>
                            <div class="mt-auto pt-6">
                                <ul class="space-y-2.5 border-t border-slate-100 pt-6 text-sm text-slate-600 dark:border-white/5 dark:text-slate-400">
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Multiple payout methods</li>
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Track every request</li>
                                </ul>
                            </div>
                        </article>

                        {{-- Live Support --}}
                        <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-sky-200 hover:shadow-xl hover:shadow-sky-900/5 lg:p-8 dark:border-white/10 dark:bg-slate-900 dark:hover:border-sky-400/30">
                            <span class="flex size-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 ring-1 ring-sky-100 ring-inset dark:bg-sky-400/10 dark:text-sky-400 dark:ring-sky-400/20">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                            </span>
                            <h3 class="mt-6 text-lg font-semibold text-slate-900 dark:text-white">Live Support</h3>
                            <p class="mt-2 leading-relaxed text-slate-600 dark:text-slate-400">Get help when you need it with our real-time chat support system.</p>
                            <div class="mt-auto pt-6">
                                <ul class="space-y-2.5 border-t border-slate-100 pt-6 text-sm text-slate-600 dark:border-white/5 dark:text-slate-400">
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Real-time chat</li>
                                    <li class="flex items-center gap-2.5"><svg class="size-4 shrink-0 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Help with every payout</li>
                                </ul>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            {{-- How it works --}}
            <section id="how-it-works" class="scroll-mt-20 py-20 sm:py-28">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">How it works</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-balance sm:text-4xl">Get paid out in three simple steps</h2>
                    </div>

                    <div class="relative mt-14">
                        <div class="absolute top-6 right-[16.66%] left-[16.66%] hidden h-px bg-linear-to-r from-transparent via-slate-300 to-transparent md:block dark:via-white/15" aria-hidden="true"></div>

                        <ol class="relative grid gap-10 md:grid-cols-3 md:gap-8">
                            @foreach ($steps as $step)
                                <li class="text-center">
                                    <span class="mx-auto flex size-12 items-center justify-center rounded-full bg-white text-lg font-bold text-blue-600 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:text-blue-400 dark:ring-white/15">{{ $loop->iteration }}</span>
                                    <h3 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">{{ $step['title'] }}</h3>
                                    <p class="mx-auto mt-2 max-w-xs leading-relaxed text-slate-600 dark:text-slate-400">{{ $step['body'] }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </section>

            {{-- Call to action --}}
            <section class="px-4 pb-20 sm:px-6 sm:pb-28 lg:px-8">
                <div class="relative isolate mx-auto max-w-6xl overflow-hidden rounded-3xl bg-linear-to-br from-blue-600 via-blue-600 to-indigo-700 px-6 py-14 text-center shadow-2xl shadow-blue-900/20 sm:px-12 sm:py-20">
                    <div class="absolute -top-24 -right-24 -z-10 size-72 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
                    <div class="absolute -bottom-32 -left-20 -z-10 size-80 rounded-full bg-indigo-400/30 blur-3xl" aria-hidden="true"></div>

                    <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-balance text-white sm:text-4xl">Ready to take control of your money?</h2>
                    <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-pretty text-blue-100 sm:text-lg">Create your account and start managing your balance and payouts today.</p>

                    <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ $getStartedUrl }}" class="inline-flex w-full max-w-xs items-center justify-center rounded-xl bg-white px-7 py-3.5 text-base font-semibold text-blue-700 shadow-lg transition hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto">
                            Get Started
                        </a>
                        <a href="{{ $signInUrl }}" class="inline-flex w-full max-w-xs items-center justify-center rounded-xl px-7 py-3.5 text-base font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto">
                            Sign In
                        </a>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-slate-200/70 dark:border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 pt-8 pb-24 text-sm text-slate-500 sm:flex-row sm:px-6 lg:px-8 dark:text-slate-400">
                <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-300">
                    <span class="flex size-7 items-center justify-center rounded-lg bg-linear-to-br from-blue-500 to-indigo-600 text-white">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </footer>

        <x-chat-widget />
    </body>
</html>
