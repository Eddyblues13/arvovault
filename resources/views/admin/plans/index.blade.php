@use('Illuminate\Support\Number')

<x-layouts.admin title="Plans">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Plans</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">Create the plans users can browse on their Plans page.</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <x-icon name="plus" class="size-4" />
            New plan
        </a>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Plans">
        @if ($plans->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="trophy" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No plans yet</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Create your first plan and it will appear on the users' Plans page.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($plans as $plan)
                    <li class="flex flex-wrap items-center gap-4 px-5 py-4 sm:flex-nowrap sm:px-6">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $plan->name }}</p>
                                @if ($plan->is_active)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-emerald-600/20 ring-inset dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">Visible</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 ring-1 ring-slate-500/20 ring-inset dark:bg-white/5 dark:text-slate-400 dark:ring-white/10">Hidden</span>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                {{ trans_choice('{0} No features|{1} :count feature|[2,*] :count features', count($plan->features ?? [])) }}
                                @if ($plan->description)
                                    · {{ $plan->description }}
                                @endif
                            </p>
                        </div>
                        <p class="text-sm font-semibold whitespace-nowrap text-slate-900 tabular-nums dark:text-white">{{ Number::currency($plan->price_in_cents / 100) }}</p>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
                            <x-icon name="pencil" class="size-3.5" />
                            Edit<span class="sr-only"> {{ $plan->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layouts.admin>
