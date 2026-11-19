<x-layouts.admin :title="'Edit '.$plan->name">
    <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All plans
    </a>

    <div class="mt-4">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Edit plan</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Changes show on the Plans page right away.</p>
    </div>

    <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="mt-8 max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900">
            @include('admin.plans.form', ['plan' => $plan])
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <x-icon name="check" class="size-4" />
                Save changes
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" data-confirm="Delete the {{ $plan->name }} plan? This can't be undone." class="mt-10 max-w-3xl border-t border-slate-200 pt-6 dark:border-white/10">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-red-600 ring-1 ring-red-200 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:text-red-400 dark:ring-red-400/30 dark:hover:bg-red-400/10">
            <x-icon name="trash" class="size-4" />
            Delete plan
        </button>
    </form>
</x-layouts.admin>
