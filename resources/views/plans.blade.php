@use('Illuminate\Support\Number')

<x-layouts.app title="Plans">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Plans</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Compare the plans available to your account.</p>
    </div>

    @if ($plans->isEmpty())
        <div class="mt-8 flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center dark:border-white/15 dark:bg-slate-900">
            <span class="flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                <x-icon name="trophy" class="size-7" />
            </span>
            <p class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">No plans available yet</p>
            <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">New plans will appear here as soon as they're ready.</p>
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($plans as $plan)
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-slate-900" aria-labelledby="plan-{{ $plan->id }}-name">
                    <div class="flex-1">
                        <h2 id="plan-{{ $plan->id }}-name" class="text-lg font-semibold text-slate-900 dark:text-white">{{ $plan->name }}</h2>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 tabular-nums dark:text-white">{{ Number::currency($plan->price_in_cents / 100) }}</p>

                        @if ($plan->description)
                            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
                        @endif

                        @if (filled($plan->features))
                            <ul class="mt-5 space-y-2.5 text-sm text-slate-700 dark:text-slate-300">
                                @foreach ($plan->features as $feature)
                                    <li class="flex items-start gap-2.5">
                                        <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                                            <x-icon name="check" class="size-3" stroke-width="3" />
                                        </span>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <button type="button" data-open-chat data-chat-message="Hi! I'd like to know more about the {{ $plan->name }} plan." class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <x-icon name="chat" class="size-4" />
                        Ask about this plan
                    </button>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.app>
