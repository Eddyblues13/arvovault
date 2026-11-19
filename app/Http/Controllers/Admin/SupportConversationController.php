<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class SupportConversationController extends Controller
{
    /**
     * List support conversations, with the most recent activity first.
     */
    public function index(): View
    {
        $latestMessage = fn (string $column): Builder => SupportMessage::query()
            ->select($column)
            ->whereColumn('support_messages.user_id', 'users.id')
            ->latest()
            ->latest('id')
            ->limit(1);

        $conversations = User::query()
            ->whereHas('supportMessages')
            ->withCount(['supportMessages as unread_messages_count' => fn (Builder $query): Builder => $query->fromUser()->unread()])
            ->withMax('supportMessages as last_message_at', 'created_at')
            ->addSelect([
                'last_message_body' => $latestMessage('body'),
                'last_message_is_from_admin' => $latestMessage('is_from_admin'),
            ])
            ->withCasts([
                'last_message_at' => 'datetime',
                'last_message_is_from_admin' => 'boolean',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.support.index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Show a user's support conversation, marking their messages as read.
     */
    public function show(User $user): View
    {
        $user->supportMessages()->fromUser()->unread()->update(['read_at' => now()]);

        return view('admin.support.show', [
            'user' => $user,
            'messages' => $user->recentSupportMessages(),
        ]);
    }
}
