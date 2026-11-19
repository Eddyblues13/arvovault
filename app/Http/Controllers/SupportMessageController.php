<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportMessageController extends Controller
{
    /**
     * Render the user's support conversation for the chat widget, marking replies as read.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $user->supportMessages()->fromAdmin()->unread()->update(['read_at' => now()]);

        return view('support.messages', [
            'messages' => $user->recentSupportMessages(),
            'viewer' => 'user',
        ]);
    }

    /**
     * Send a message to the support team.
     */
    public function store(StoreSupportMessageRequest $request): JsonResponse|RedirectResponse
    {
        $request->user()->supportMessages()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Message sent.'], 201);
        }

        return back()->with('status', 'Message sent. Our team will reply in the chat.');
    }
}
