@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">

        <title>{{ $title }} · {{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="flex min-h-screen">
            {{-- Form column --}}
            <div class="relative isolate flex flex-1 flex-col px-4 py-6 sm:px-6 lg:px-12">
                <div class="hero-grid absolute inset-0 -z-10 lg:hidden" aria-hidden="true"></div>

                <header>
                    <x-app-logo class="w-fit" />
                </header>

                <main class="flex flex-1 items-center justify-center py-12">
                    <div class="w-full max-w-sm transition duration-500 ease-out starting:translate-y-3 starting:opacity-0 motion-reduce:transition-none">
                        {{ $slot }}
                    </div>
                </main>

                <footer class="text-center text-xs text-slate-500 lg:text-left dark:text-slate-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </footer>
            </div>

            {{-- Brand panel --}}
            <aside class="relative isolate hidden w-[45%] max-w-2xl flex-col justify-between overflow-hidden bg-linear-to-br from-blue-600 via-blue-600 to-indigo-700 p-12 text-white lg:flex xl:p-16">
                <div class="absolute -top-32 -right-32 -z-10 size-96 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
                <div class="absolute -bottom-40 -left-24 -z-10 size-96 rounded-full bg-indigo-400/30 blur-3xl" aria-hidden="true"></div>

                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-sm font-medium ring-1 ring-white/20 ring-inset">
                    <x-icon name="shield-check" class="size-4" />
                    Fast, secure &amp; simple payouts
                </span>

                <div>
                    <h2 class="max-w-md text-4xl leading-tight font-bold tracking-tight text-balance xl:text-5xl">Your secure wallet &amp; payout platform</h2>
                    <p class="mt-5 max-w-md text-lg leading-relaxed text-blue-100">Manage your balance, request withdrawals, and track your transactions all in one place.</p>

                    <ul class="mt-10 space-y-4">
                        @foreach (['Track your balance and earnings in real time', 'Withdraw to Cash App, Zelle, PayPal, crypto and more', 'Get help fast with live support'] as $benefit)
                            <li class="flex items-center gap-3">
                                <span class="flex size-7 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20 ring-inset">
                                    <x-icon name="check" class="size-4" stroke-width="2.5" />
                                </span>
                                <span class="text-blue-50">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm ring-inset">
                    <p class="text-xs font-semibold tracking-wider text-blue-100 uppercase">Withdraw to</p>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach (['Cash App', 'Zelle', 'PayPal', 'Crypto', 'and more'] as $payoutMethod)
                            <li class="rounded-full bg-white/15 px-3 py-1 text-sm font-medium">{{ $payoutMethod }}</li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </body>
</html>
