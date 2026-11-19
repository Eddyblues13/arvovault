@if (session('status'))
    <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
        <x-icon name="check" class="mt-0.5 size-4" />
        <p>{{ session('status') }}</p>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-200">
        <x-icon name="x" class="mt-0.5 size-4" />
        <p>{{ session('error') }}</p>
    </div>
@endif
