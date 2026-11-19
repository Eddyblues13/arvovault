<details id="chat-widget" class="group fixed right-4 bottom-4 z-50 sm:right-6 sm:bottom-6">
    <summary class="relative flex cursor-pointer list-none items-center gap-2 rounded-full bg-linear-to-r from-blue-600 to-indigo-600 p-1.5 text-sm font-semibold text-white shadow-xl shadow-blue-900/25 ring-1 ring-white/10 transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:pl-5 [&::-webkit-details-marker]:hidden">
        <span class="hidden sm:inline sm:group-open:hidden">Chat</span>
        <span class="hidden sm:group-open:inline">Close</span>
        <span class="flex size-11 items-center justify-center rounded-full bg-white/15">
            <svg class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
            <x-icon name="x" class="hidden size-5 group-open:block" stroke-width="2.5" />
        </span>
        @if ($unreadSupportRepliesCount > 0)
            <span data-chat-unread class="absolute -top-1 -right-1 flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] leading-5 font-semibold text-white tabular-nums ring-2 ring-white group-open:hidden dark:ring-slate-950">{{ $unreadSupportRepliesCount }}<span class="sr-only"> unread {{ Str::plural('reply', $unreadSupportRepliesCount) }}</span></span>
        @else
            <span class="absolute -bottom-0.5 -left-0.5 size-3.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-950" aria-hidden="true"></span>
        @endif
        <span class="sr-only">Toggle live support chat</span>
    </summary>

    <div class="absolute right-0 bottom-full mb-3 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/15 dark:border-white/10 dark:bg-slate-900">
        <div class="flex items-center gap-3 border-b border-slate-100 p-4 dark:border-white/5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400">
                <x-icon name="chat" />
            </span>
            <div>
                <p class="font-semibold text-slate-900 dark:text-white">Live Support</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Our team is here to help.</p>
            </div>
        </div>

        @auth
            <div data-chat data-chat-url="{{ route('support.messages.index') }}">
                <div data-chat-messages class="h-72 overflow-y-auto px-4 py-3" aria-live="polite">
                    <p class="py-10 text-center text-sm text-slate-500 dark:text-slate-400">Loading your conversation…</p>
                </div>

                <form method="POST" action="{{ route('support.messages.store') }}" data-chat-form class="border-t border-slate-100 p-3 dark:border-white/5">
                    @csrf
                    <label for="chat-message" class="sr-only">Message</label>
                    <div class="flex items-end gap-2">
                        <textarea id="chat-message" name="body" rows="2" maxlength="2000" required placeholder="Type your message…" class="block w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 focus:outline-none dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-500"></textarea>
                        <button type="submit" class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-60">
                            <span class="sr-only">Send message</span>
                            <x-icon name="send" class="size-4" />
                        </button>
                    </div>
                    <p data-chat-error role="alert" class="mt-2 hidden text-xs text-red-600 dark:text-red-400"></p>
                </form>
            </div>
        @else
            <div class="p-5">
                <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">Sign in to start a conversation with our support team.</p>
                <a href="{{ route('login') }}" class="mt-4 flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Sign in to chat</a>
            </div>
        @endauth
    </div>
</details>
