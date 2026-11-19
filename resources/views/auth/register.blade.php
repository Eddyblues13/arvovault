<x-layouts.guest title="Create account">
    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Create your account</h1>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Start managing your balance and payouts in minutes.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <x-input-field label="Full name" name="name" autocomplete="name" placeholder="Jane Doe" required autofocus />

        <x-input-field label="Email address" name="email" type="email" autocomplete="email" placeholder="you@example.com" required />

        <x-input-field label="Password" name="password" type="password" autocomplete="new-password" placeholder="Create a password" hint="Use at least 8 characters." required />

        <x-input-field label="Confirm password" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat your password" required />

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Create account
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Sign in</a>
    </p>
</x-layouts.guest>
