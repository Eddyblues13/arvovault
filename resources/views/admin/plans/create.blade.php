<x-layouts.admin title="New plan">
    <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All plans
    </a>

    <div class="mt-4">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">New plan</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Users see visible plans on their Plans page.</p>
    </div>

    <form method="POST" action="{{ route('admin.plans.store') }}" class="mt-8 max-w-3xl space-y-6">
        @csrf

        <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900">
            @include('admin.plans.form', ['plan' => null])
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <x-icon name="plus" class="size-4" />
                Create plan
            </button>
        </div>
    </form>
</x-layouts.admin>
