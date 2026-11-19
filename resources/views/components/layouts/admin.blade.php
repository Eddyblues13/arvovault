@props(['title' => 'Dashboard'])

@php
    $navigationSections = [
        'Manage' => [
            ['route' => 'admin.dashboard', 'activePattern' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'admin.users.index', 'activePattern' => 'admin.users.*', 'label' => 'Users', 'icon' => 'users'],
            ['route' => 'admin.transactions.index', 'activePattern' => 'admin.transactions.*', 'label' => 'Transactions', 'icon' => 'arrow-left-right'],
            ['route' => 'admin.support.index', 'activePattern' => 'admin.support.*', 'label' => 'Support', 'icon' => 'chat'],
            ['route' => 'admin.bookings.index', 'activePattern' => 'admin.bookings.*', 'label' => 'Bookings', 'icon' => 'calendar'],
        ],
        'Content' => [
            ['route' => 'admin.plans.index', 'activePattern' => 'admin.plans.*', 'label' => 'Plans', 'icon' => 'trophy'],
            ['route' => 'admin.tips.index', 'activePattern' => 'admin.tips.*', 'label' => 'Tips', 'icon' => 'lightbulb'],
        ],
    ];

    $admin = auth('admin')->user();
@endphp

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
        {{-- Top bar --}}
        <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur-lg dark:border-white/10 dark:bg-slate-950/80">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
                <div class="flex min-w-0 items-center gap-2">
                    <button type="button" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="false" class="-ml-2 rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 lg:hidden dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                        <span class="sr-only">Open navigation</span>
                        <x-icon name="menu" class="size-6" />
                    </button>
                    <x-app-logo :href="route('admin.dashboard')" />
                    <span class="shrink-0 rounded-md bg-slate-900 px-2 py-0.5 text-[11px] font-semibold tracking-wide text-white uppercase dark:bg-white dark:text-slate-900">Admin</span>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Log out" class="flex rounded-lg p-2.5 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:bg-white/10 dark:hover:text-white">
                        <span class="sr-only">Log out</span>
                        <x-icon name="logout" />
                    </button>
                </form>
            </div>
        </header>

        {{-- Mobile backdrop --}}
        <div id="sidebar-backdrop" data-sidebar-toggle class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm data-open:block lg:hidden" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-out data-open:translate-x-0 lg:top-16 lg:z-30 lg:w-64 lg:translate-x-0 dark:border-white/10 dark:bg-slate-950">
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4 lg:hidden dark:border-white/10">
                <x-app-logo :href="route('admin.dashboard')" />
                <button type="button" data-sidebar-toggle class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-white/10 dark:hover:text-white">
                    <span class="sr-only">Close navigation</span>
                    <x-icon name="x" />
                </button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto p-4" aria-label="Admin">
                @foreach ($navigationSections as $sectionLabel => $navigation)
                    <div>
                        <p class="px-4 pb-2 text-xs font-semibold tracking-wider text-slate-400 uppercase dark:text-slate-500">{{ $sectionLabel }}</p>
                        <ul class="space-y-1">
                            @foreach ($navigation as $item)
                                @php
                                    $isActive = request()->routeIs($item['activePattern']);
                                    $waitingCount = $navigationCounts[$item['route']] ?? 0;
                                @endphp
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
                                        @if ($waitingCount > 0)
                                            <span @class([
                                                'ml-auto rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums',
                                                'bg-white/20 text-white' => $isActive,
                                                'bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300' => ! $isActive,
                                            ])>{{ $waitingCount }}<span class="sr-only"> waiting</span></span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="border-t border-slate-200 pt-4 dark:border-white/10">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white">
                        <x-icon name="external-link" />
                        View site
                    </a>
                </div>
            </nav>

            <div class="border-t border-slate-200 p-4 dark:border-white/10">
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-white/5">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white dark:bg-white dark:text-slate-900">{{ $admin->initials() }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $admin->name }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $admin->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="lg:pl-64">
            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
                <x-flash-messages />

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
