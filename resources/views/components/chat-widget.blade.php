<details id="chat-widget" class="group fixed right-4 bottom-4 z-50 sm:right-6 sm:bottom-6">
    <summary class="relative flex cursor-pointer list-none items-center gap-2 rounded-full bg-linear-to-r from-blue-600 to-indigo-600 p-1.5 text-sm font-semibold text-white shadow-xl shadow-blue-900/25 ring-1 ring-white/10 transition hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:pl-5 [&::-webkit-details-marker]:hidden">
        <span class="hidden sm:inline sm:group-open:hidden">Chat</span>
        <span class="hidden sm:group-open:inline">Close</span>
        <span class="flex size-11 items-center justify-center rounded-full bg-white/15">
            <svg class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
            <x-icon name="x" class="hidden size-5 group-open:block" stroke-width="2.5" />
        </span>
        <span class="absolute -bottom-0.5 -left-0.5 size-3.5 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-950" aria-hidden="true"></span>
        <span class="sr-only">Toggle live support chat</span>
    </summary>

    <div class="absolute right-0 bottom-full mb-3 w-[min(20rem,calc(100vw-2rem))] rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl shadow-slate-900/15 dark:border-white/10 dark:bg-slate-900">
        <div class="flex items-center gap-3">
            <span class="flex size-10 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400">
                <x-icon name="chat" />
            </span>
            <div>
                <p class="font-semibold text-slate-900 dark:text-white">Live Support</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Our team is here to help.</p>
            </div>
        </div>
        @auth
            <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Live chat is launching soon. When it's ready, you'll be able to message our support team right here.</p>
        @else
            <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Sign in to start a conversation with our support team.</p>
            <a href="{{ route('login') }}" class="mt-4 flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Sign in to chat</a>
        @endauth
    </div>
</details>
