@use('Illuminate\Support\Str')

<x-layouts.admin title="Support">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Support</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Reply to users who message the live chat.</p>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Conversations">
        @if ($conversations->isEmpty())
            <div class="flex flex-col items-center px-6 py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                    <x-icon name="chat" class="size-7" />
                </span>
                <p class="mt-4 font-semibold text-slate-900 dark:text-white">No conversations yet</p>
                <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">When a user messages the live chat, the conversation shows up here.</p>
            </div>
        @else
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach ($conversations as $user)
                    <li>
                        <a href="{{ route('admin.support.show', $user) }}" class="flex items-center gap-3 px-5 py-4 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 sm:gap-4 sm:px-6 dark:hover:bg-white/5">
                            <x-admin.user-avatar :user="$user" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-3">
                                    <p @class([
                                        'truncate text-sm text-slate-900 dark:text-white',
                                        'font-bold' => $user->unread_messages_count > 0,
                                        'font-semibold' => $user->unread_messages_count === 0,
                                    ])>{{ $user->name }}</p>
                                    <p class="shrink-0 text-xs text-slate-500 dark:text-slate-400">{{ $user->last_message_at?->diffForHumans() }}</p>
                                </div>
                                <div class="mt-0.5 flex items-center justify-between gap-3">
                                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $user->last_message_is_from_admin ? 'You: ' : '' }}{{ Str::squish((string) $user->last_message_body) }}</p>
                                    @if ($user->unread_messages_count > 0)
                                        <span class="shrink-0 rounded-full bg-blue-600 px-2 py-0.5 text-xs font-semibold text-white tabular-nums">{{ $user->unread_messages_count }}<span class="sr-only"> unread</span></span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($conversations->hasPages())
        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    @endif
</x-layouts.admin>
