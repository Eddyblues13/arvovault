@use('Illuminate\Support\Number')

<x-layouts.admin title="Users">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Users</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">
                @if ($search !== '')
                    {{ trans_choice('{0} No users match|{1} :count user matches|[2,*] :count users match', $users->total()) }} “{{ $search }}”
                @else
                    {{ trans_choice('{0} No registered accounts yet|{1} :count registered account|[2,*] :count registered accounts', $users->total()) }}
                @endif
            </p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" role="search" class="flex w-full gap-2 sm:w-96">
            <label for="search" class="sr-only">Search users by name or email</label>
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-slate-400" />
                <input id="search" type="search" name="search" value="{{ $search }}" placeholder="Search name or email" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pr-3 pl-10 text-sm text-slate-900 shadow-xs transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 focus:outline-none dark:border-white/15 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-blue-400">
            </div>
            <button type="submit" class="rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Search</button>
        </form>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Users">
        @if ($users->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="users" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No users found</p>
                @if ($search !== '')
                    <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Try a different name or email address.</p>
                    <a href="{{ route('admin.users.index') }}" class="mt-4 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Clear search</a>
                @else
                    <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">Users will appear here as soon as they create an account.</p>
                @endif
            </div>
        @else
            <div class="hidden grid-cols-12 gap-4 border-b border-slate-100 px-6 py-3 text-xs font-semibold tracking-wider text-slate-500 uppercase md:grid dark:border-white/5 dark:text-slate-400" aria-hidden="true">
                <span class="col-span-6">User</span>
                <span class="col-span-3 text-right">Available balance</span>
                <span class="col-span-3 text-right">Joined</span>
            </div>

            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($users as $user)
                    <li>
                        <a href="{{ route('admin.users.show', $user) }}" class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 px-5 py-4 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 sm:px-6 md:grid-cols-12 dark:hover:bg-white/5">
                            <div class="flex min-w-0 items-center gap-3 md:col-span-6">
                                <x-admin.user-avatar :user="$user" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right md:col-span-3">
                                <p class="text-sm font-semibold whitespace-nowrap text-slate-900 tabular-nums dark:text-white">{{ Number::currency($user->available_balance_in_cents / 100) }}</p>
                                <p class="text-xs whitespace-nowrap text-slate-500 md:hidden dark:text-slate-400">Joined {{ $user->created_at->format('M j, Y') }}</p>
                            </div>
                            <p class="hidden text-right text-sm text-slate-500 md:col-span-3 md:block dark:text-slate-400">{{ $user->created_at->format('M j, Y') }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</x-layouts.admin>
