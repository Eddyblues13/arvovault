<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportMessageRequest;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupportMessageController extends Controller
{
    /**
     * Render a user's support conversation for the admin chat, marking their messages as read.
     */
    public function index(User $user): View
    {
        $user->supportMessages()->fromUser()->unread()->update(['read_at' => now()]);

        return view('support.messages', [
            'messages' => $user->recentSupportMessages(),
            'viewer' => 'admin',
            'customerName' => $user->name,
        ]);
    }

    /**
     * Reply to a user's support conversation as the signed-in admin.
     */
    public function store(StoreSupportMessageRequest $request, User $user): JsonResponse|RedirectResponse
    {
        $message = new SupportMessage($request->validated());
        $message->is_from_admin = true;
        $message->admin()->associate($request->user('admin'));

        $user->supportMessages()->save($message);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Reply sent.'], 201);
        }

        return redirect()->route('admin.support.show', $user)->with('status', 'Reply sent.');
    }
}
