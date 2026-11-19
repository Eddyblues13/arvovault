<x-layouts.guest title="Reset password">
    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Choose a new password</h1>
    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">You'll use it the next time you sign in.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-input-field label="Email address" name="email" type="email" :value="$email" autocomplete="email" required />

        <x-input-field label="New password" name="password" type="password" autocomplete="new-password" placeholder="Create a password" hint="Use at least 8 characters." required autofocus />

        <x-input-field label="Confirm new password" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Repeat your password" required />

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            Reset password
            <x-icon name="arrow-right" class="size-4" />
        </button>
    </form>
</x-layouts.guest>
