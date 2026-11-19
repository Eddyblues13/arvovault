@props(['title' => 'Dashboard'])

@php
    $navigation = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'wallet', 'label' => 'Wallet', 'icon' => 'wallet'],
        ['route' => 'withdraw', 'label' => 'Withdraw', 'icon' => 'withdraw'],
        ['route' => 'plans', 'label' => 'Plans', 'icon' => 'trophy'],
        ['route' => 'tips', 'label' => 'Tips', 'icon' => 'lightbulb'],
        ['route' => 'bookings', 'label' => 'Bookings', 'icon' => 'calendar'],
    ];

    $user = auth()->user();
@endphp

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
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        {{-- Top bar --}}
        <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur-lg dark:border-white/10 dark:bg-slate-950/80">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <button type="button" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="false" class="-ml-2 rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 lg:hidden dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                        <span class="sr-only">Open navigation</span>
                        <x-icon name="menu" class="size-6" />
                    </button>
                    <x-app-logo />
                </div>

                <div class="flex items-center gap-1">
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none rounded-lg p-2.5 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 group-open:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white dark:group-open:bg-white/10 [&::-webkit-details-marker]:hidden">
                            <span class="sr-only">Notifications</span>
                            <x-icon name="bell" />
                        </summary>
                        <div class="absolute right-0 mt-2 w-[min(18rem,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-900/10 dark:border-white/10 dark:bg-slate-900">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</p>
                            <div class="mt-3 flex flex-col items-center rounded-xl bg-slate-50 px-4 py-6 text-center dark:bg-white/5">
                                <x-icon name="bell" class="size-6 text-slate-400" />
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">You're all caught up.</p>
                            </div>
                        </div>
                    </details>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log out" class="flex rounded-lg p-2.5 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                            <span class="sr-only">Log out</span>
                            <x-icon name="logout" />
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Mobile backdrop --}}
        <div id="sidebar-backdrop" data-sidebar-toggle class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm data-open:block lg:hidden" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-out data-open:translate-x-0 lg:top-16 lg:z-30 lg:w-64 lg:translate-x-0 dark:border-white/10 dark:bg-slate-950">
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 lg:hidden dark:border-white/10">
                <x-app-logo />
                <button type="button" data-sidebar-toggle class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-white/10 dark:hover:text-white">
                    <span class="sr-only">Close navigation</span>
                    <x-icon name="x" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4" aria-label="Main">
                <ul class="space-y-1">
                    @foreach ($navigation as $item)
                        @php($isActive = request()->routeIs($item['route']))
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @if ($isActive) aria-current="page" @endif
                                @class([
                                    'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-blue-600',
                                    'bg-blue-600 text-white shadow-md shadow-blue-600/25' => $isActive,
                                    'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' => ! $isActive,
                                ])
                            >
                                <x-icon :name="$item['icon']" />
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="border-t border-slate-200 p-4 dark:border-white/10">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-white/5">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-blue-500 to-indigo-600 text-sm font-semibold text-white">{{ $user->initials() }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="lg:pl-64">
            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
                {{ $slot }}
            </main>
        </div>

        <x-chat-widget />
    </body>
</html>
