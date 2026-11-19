<x-layouts.admin :title="'Edit '.$user->name">
    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex max-w-full items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        <span class="truncate">Back to {{ $user->name }}</span>
    </a>

    <div class="mt-4">
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Edit user</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Update the account details for {{ $user->name }}.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-8 max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="profile-heading">
            <h2 id="profile-heading" class="text-base font-semibold text-slate-900 dark:text-white">Profile</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <x-input-field label="Full name" name="name" :value="$user->name" autocomplete="off" required />

                <x-input-field label="Email address" name="email" type="email" :value="$user->email" autocomplete="off" required />
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="password-heading">
            <h2 id="password-heading" class="text-base font-semibold text-slate-900 dark:text-white">Password</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Leave both fields blank to keep the current password.</p>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <x-input-field label="New password" name="password" type="password" autocomplete="new-password" hint="Use at least 8 characters." />

                <x-input-field label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" />
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">Cancel</a>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <x-icon name="check" class="size-4" />
                Save changes
            </button>
        </div>
    </form>
</x-layouts.admin>
