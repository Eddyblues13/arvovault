@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title }} · Admin · {{ config('app.name', 'Laravel') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="relative isolate flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6">
            <div class="hero-grid absolute inset-0 -z-10" aria-hidden="true"></div>

            <div class="w-full max-w-sm transition duration-500 ease-out starting:translate-y-3 starting:opacity-0 motion-reduce:transition-none">
                <div class="flex flex-col items-center gap-4">
                    <x-app-logo />
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-slate-900">
                        <x-icon name="lock" class="size-3.5" />
                        Admin area
                    </span>
                </div>

                <main class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-8 dark:border-white/10 dark:bg-slate-900">
                    {{ $slot }}
                </main>

                <footer class="mt-8 text-center text-xs text-slate-500 dark:text-slate-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </footer>
            </div>
        </div>
    </body>
</html>
