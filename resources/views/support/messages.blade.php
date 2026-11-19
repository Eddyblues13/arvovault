{{--
    A support conversation, shared by the user's chat widget and the admin inbox.
    Expects $messages (oldest first), $viewer ('user' or 'admin') and, for admins, $customerName.
--}}
@if ($messages->isEmpty())
    <div class="flex h-full flex-col items-center justify-center py-8 text-center">
        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $viewer === 'admin' ? 'No messages yet' : 'How can we help?' }}</p>
        <p class="mt-1 max-w-60 text-xs text-slate-500 dark:text-slate-400">{{ $viewer === 'admin' ? 'Send a message to start the conversation.' : 'Send us a message and our team will reply right here.' }}</p>
    </div>
@else
    <ol class="space-y-3">
        @foreach ($messages as $message)
            @php
                $isOwnMessage = $viewer === 'admin' ? $message->is_from_admin : ! $message->is_from_admin;
                $senderName = match (true) {
                    $viewer === 'user' => $message->is_from_admin ? 'Support' : 'You',
                    $message->is_from_admin => $message->admin?->name ?? 'Support',
                    default => $customerName ?? 'Customer',
                };
            @endphp
            <li @class(['flex flex-col', 'items-end' => $isOwnMessage, 'items-start' => ! $isOwnMessage])>
                <div @class([
                    'max-w-[85%] rounded-2xl px-3.5 py-2 text-sm whitespace-pre-line wrap-break-word',
                    'rounded-br-md bg-blue-600 text-white' => $isOwnMessage,
                    'rounded-bl-md bg-slate-100 text-slate-800 dark:bg-white/10 dark:text-slate-100' => ! $isOwnMessage,
                ])>{{ $message->body }}</div>
                <p class="mt-1 px-1 text-[11px] text-slate-400 dark:text-slate-500">{{ $senderName }} · {{ $message->created_at->format('M j, g:i A') }}</p>
            </li>
        @endforeach
    </ol>
@endif
