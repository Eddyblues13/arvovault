<x-layouts.admin title="Admins">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Admins</h1>
            <p class="mt-1 text-slate-600 dark:text-slate-400">Everyone who can sign in to the admin area.</p>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <x-icon name="plus" class="size-4" />
            Add admin
        </a>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Admins">
        <ul class="divide-y divide-slate-100 dark:divide-white/5">
            @foreach ($admins as $admin)
                @php($isCurrentAdmin = $admin->is($currentAdmin))
                <li class="flex flex-wrap items-center gap-3 px-5 py-4 sm:flex-nowrap sm:gap-4 sm:px-6">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white dark:bg-white dark:text-slate-900" aria-hidden="true">{{ $admin->initials() }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $admin->name }}</p>
                            @if ($isCurrentAdmin)
                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700 ring-1 ring-blue-600/20 ring-inset dark:bg-blue-400/10 dark:text-blue-300 dark:ring-blue-400/20">You</span>
                            @endif
                        </div>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $admin->email }} · Added {{ $admin->created_at->format('M j, Y') }}</p>
                    </div>

                    @unless ($isCurrentAdmin)
                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" data-confirm="Remove {{ $admin->name }}'s admin access?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-red-600 ring-1 ring-red-200 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:text-red-400 dark:ring-red-400/30 dark:hover:bg-red-400/10">
                                <x-icon name="trash" class="size-3.5" />
                                Remove<span class="sr-only"> {{ $admin->name }}</span>
                            </button>
                        </form>
                    @endunless
                </li>
            @endforeach
        </ul>
    </section>
</x-layouts.admin>
