<x-layouts.guest title="Sign in">
    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Welcome back</h1>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Sign in to manage your balance and payouts.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <x-input-field label="Email address" name="email" type="email" autocomplete="email" placeholder="you@example.com" required autofocus />

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

    <p class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Create one</a>
    </p>
</x-layouts.guest>
