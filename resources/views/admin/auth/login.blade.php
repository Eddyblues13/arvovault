<x-layouts.admin-guest title="Sign in">
    <h1 class="text-xl font-bold tracking-tight sm:text-2xl">Admin sign in</h1>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Sign in with your admin account to manage users and funds.</p>

    @if ($hasNoAdmins)
        <div class="mt-6 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:bg-amber-400/10 dark:text-amber-100">
            <p class="font-semibold">No admin accounts exist yet.</p>
            <p class="mt-1">Create the first one by running <code class="rounded bg-amber-100 px-1 py-0.5 font-mono text-xs dark:bg-amber-400/20">php artisan admin:create</code> on the server.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5">
        @csrf

        <x-input-field label="Email address" name="email" type="email" autocomplete="username" placeholder="admin@example.com" required autofocus />

        <x-input-field label="Password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required />

        <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-slate-300 accent-blue-600">
            Remember me
        </label>

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Sign In
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>
</x-layouts.admin-guest>
