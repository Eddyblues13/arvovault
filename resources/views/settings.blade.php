<x-layouts.app title="Settings">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Settings</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Manage your profile and password.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="profile-heading">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                    <x-icon name="user" />
                </span>
                <div>
                    <h2 id="profile-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Profile</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Your name and the email you sign in with.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-input-field label="Full name" name="name" :value="$user->name" autocomplete="name" required />

                <x-input-field label="Email address" name="email" type="email" :value="$user->email" autocomplete="email" required />

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto">
                    <x-icon name="check" class="size-4" />
                    Save profile
                </button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="password-heading">
            <div class="flex items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 ring-inset dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20">
                    <x-icon name="key" />
                </span>
                <div>
                    <h2 id="password-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Password</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Use a long password you don't use anywhere else.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-input-field label="Current password" name="current_password" type="password" autocomplete="current-password" required />

                <x-input-field label="New password" name="password" type="password" autocomplete="new-password" hint="Use at least 8 characters." required />

                <x-input-field label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" required />

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 sm:w-auto dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200">
                    <x-icon name="key" class="size-4" />
                    Update password
                </button>
            </form>
        </section>
    </div>
</x-layouts.app>
