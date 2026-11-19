<x-layouts.admin title="Add admin">
    <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All admins
    </a>

    <div class="mt-4">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Add admin</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">They can sign in to the admin area with this email and password.</p>
    </div>

    <form method="POST" action="{{ route('admin.admins.store') }}" class="mt-8 max-w-3xl space-y-6">
        @csrf

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-input-field label="Full name" name="name" autocomplete="off" required />

                <x-input-field label="Email address" name="email" type="email" autocomplete="off" required />

                <x-input-field label="Password" name="password" type="password" autocomplete="new-password" hint="Use at least 8 characters. Share it with them securely." required />

                <x-input-field label="Confirm password" name="password_confirmation" type="password" autocomplete="new-password" required />
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">Cancel</a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <x-icon name="plus" class="size-4" />
                Add admin
            </button>
        </div>
    </form>
</x-layouts.admin>
