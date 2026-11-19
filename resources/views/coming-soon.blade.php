<x-layouts.app :title="$page">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $page }}</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">{{ $description }}</p>
    </div>

    <div class="mt-8 flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center dark:border-white/15 dark:bg-slate-900">
        <span class="flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
            <x-icon name="sparkles" class="size-7" />
        </span>
        <p class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">Coming soon</p>
        <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">We're putting the finishing touches on this page. Check back shortly.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
            <x-icon name="arrow-left" class="size-4" />
            Back to dashboard
        </a>
    </div>
</x-layouts.app>
