<x-layouts.app title="Tips">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Tips</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Helpful guides for getting the most out of your wallet.</p>
    </div>

    @if ($tips->isEmpty())
        <div class="mt-8 flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center dark:border-white/15 dark:bg-slate-900">
            <span class="flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                <x-icon name="lightbulb" class="size-7" />
            </span>
            <p class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">No tips yet</p>
            <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">Our team is writing guides for you. Check back soon.</p>
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($tips as $tip)
                <article class="group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-white/10 dark:bg-slate-900 dark:hover:border-blue-400/40">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 ring-inset dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                        <x-icon name="lightbulb" />
                    </span>
                    <h2 class="mt-4 font-semibold text-slate-900 dark:text-white">
                        <a href="{{ route('tips.show', $tip) }}" class="focus-visible:outline-none">
                            <span class="absolute inset-0 rounded-2xl group-has-focus-visible:outline-2 group-has-focus-visible:outline-offset-2 group-has-focus-visible:outline-blue-600" aria-hidden="true"></span>
                            {{ $tip->title }}
                        </a>
                    </h2>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $tip->excerpt() }}</p>
                    <p class="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                        <time datetime="{{ $tip->published_at->toDateString() }}">{{ $tip->published_at->format('M j, Y') }}</time>
                        <span class="inline-flex items-center gap-1 font-semibold text-blue-600 dark:text-blue-400">
                            Read tip
                            <x-icon name="arrow-right" class="size-3.5" />
                        </span>
                    </p>
                </article>
            @endforeach
        </div>

        @if ($tips->hasPages())
            <div class="mt-6">
                {{ $tips->links() }}
            </div>
        @endif
    @endif
</x-layouts.app>
