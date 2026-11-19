<x-layouts.admin :title="'Support · '.$user->name">
    <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-medium text-slate-500 transition hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-400 dark:hover:text-white">
        <x-icon name="arrow-left" class="size-4" />
        All conversations
    </a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <x-admin.user-avatar :user="$user" class="size-12 text-base" />
            <div class="min-w-0">
                <h1 class="truncate text-xl font-bold tracking-tight sm:text-2xl">{{ $user->name }}</h1>
                <p class="truncate text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</p>
            </div>
        </div>

        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-white/5 dark:text-slate-200 dark:ring-white/10 dark:hover:bg-white/10">
            <x-icon name="user" class="size-4" />
            View account
        </a>
    </div>

    <section data-chat data-chat-autostart data-chat-url="{{ route('admin.support.messages.index', $user) }}" class="mt-6 flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900" aria-label="Conversation with {{ $user->name }}">
        <div data-chat-messages class="h-[min(32rem,60vh)] overflow-y-auto px-4 py-4 sm:px-6" aria-live="polite">
            @include('support.messages', ['messages' => $messages, 'viewer' => 'admin', 'customerName' => $user->name])
        </div>

        <form method="POST" action="{{ route('admin.support.messages.store', $user) }}" data-chat-form class="border-t border-slate-100 p-3 sm:p-4 dark:border-white/5">
            @csrf
            <label for="chat-message" class="sr-only">Reply</label>
            <div class="flex items-end gap-2">
                <textarea id="chat-message" name="body" rows="2" maxlength="2000" required placeholder="Write a reply…" class="block w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 focus:outline-none dark:border-white/15 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-500"></textarea>
                <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-60">
                    <x-icon name="send" class="size-4" />
                    <span class="hidden sm:inline">Send</span>
                    <span class="sr-only sm:hidden">Send reply</span>
                </button>
            </div>
            @error('body')
                <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p data-chat-error role="alert" class="mt-2 hidden text-xs text-red-600 dark:text-red-400"></p>
        </form>
    </section>
</x-layouts.admin>
