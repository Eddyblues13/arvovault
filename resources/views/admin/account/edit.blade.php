<x-layouts.admin title="My account">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">My account</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Update the details you sign in to the admin area with.</p>
    </div>

    <form method="POST" action="{{ route('admin.account.update') }}" class="mt-8 max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="details-heading">
            <h2 id="details-heading" class="text-base font-semibold text-slate-900 dark:text-white">Details</h2>

            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-input-field label="Full name" name="name" :value="$admin->name" autocomplete="name" required />

                <x-input-field label="Email address" name="email" type="email" :value="$admin->email" autocomplete="email" required />
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="password-heading">
            <h2 id="password-heading" class="text-base font-semibold text-slate-900 dark:text-white">Change password</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Leave these blank to keep your current password.</p>

            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-field label="Current password" name="current_password" type="password" autocomplete="current-password" />
                </div>

                <x-input-field label="New password" name="password" type="password" autocomplete="new-password" hint="Use at least 8 characters." />

                <x-input-field label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" />
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto">
                <x-icon name="check" class="size-4" />
                Save changes
            </button>
        </div>
    </form>
</x-layouts.admin>
