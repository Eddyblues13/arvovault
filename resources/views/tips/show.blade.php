<x-layouts.app :title="$tip->title">
    <a href="{{ route('tips') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All tips
    </a>

    <article class="mt-4 max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10 dark:border-white/10 dark:bg-slate-900">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            <time datetime="{{ $tip->published_at->toDateString() }}">{{ $tip->published_at->format('F j, Y') }}</time>
        </p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-balance sm:text-3xl">{{ $tip->title }}</h1>
        <div class="mt-6 text-base leading-relaxed whitespace-pre-line text-slate-700 dark:text-slate-300">{{ $tip->body }}</div>
    </article>
</x-layouts.app>
