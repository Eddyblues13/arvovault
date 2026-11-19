<x-layouts.guest title="Forgot password">
    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Forgot your password?</h1>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Enter your email address and we'll send you a link to choose a new one.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <x-input-field label="Email address" name="email" type="email" autocomplete="email" placeholder="you@example.com" required autofocus />

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Email reset link
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Back to sign in</a>
    </p>
</x-layouts.guest>
