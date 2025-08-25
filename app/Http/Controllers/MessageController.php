<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Get messages for a chat
     */
    public function index(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();

        // Check if user is participant
        if (!$chat->participants()->where('user_id', $user->id)->where('is_active', true)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $messages = $chat->messages()
            ->with('user')
            ->visible()
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Send a message
     */
    public function store(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();

        // Check if user is participant and chat is active
        $participant = $chat->participants()->where('user_id', $user->id)->where('is_active', true)->first();
        if (!$participant) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        if ($chat->status !== 'active') {
            return response()->json(['message' => 'Chat is not active'], 400);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'nullable|in:text,system,file,image',
            'metadata' => 'nullable|array',
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'metadata' => $request->metadata,
        ]);

        $message->load('user');

        return response()->json([
            'message' => $message,
            'status' => 'Message sent successfully'
        ], 201);
    }

    /**
     * Hide a message (soft delete for subordinates)
     */
    public function hide(Request $request, Message $message): JsonResponse
    {
        $user = $request->user();

        // Check if user is participant of the chat
        $chatParticipant = $message->chat->participants()->where('user_id', $user->id)->first();
        if (!$chatParticipant) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        // Check if user has higher role than message author
        $messageAuthorParticipant = $message->chat->participants()->where('user_id', $message->user_id)->first();
        
        if (!$messageAuthorParticipant || $chatParticipant->role === 'participant') {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        // Only admin/moderator can hide messages from participants
        if ($messageAuthorParticipant->role === 'admin' && $chatParticipant->role !== 'admin') {
            return response()->json(['message' => 'Cannot hide admin messages'], 403);
        }

        $message->update([
            'is_hidden' => true,
            'hidden_by' => $user->id,
            'hidden_at' => now(),
        ]);

        return response()->json([
            'message' => 'Message hidden successfully'
        ]);
    }

    /**
     * Send system message
     */
    public function sendSystemMessage(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();

        // Check if user is admin of the chat
        $participant = $chat->participants()->where('user_id', $user->id)->first();
        if (!$participant || $participant->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => 'system',
        ]);

        $message->load('user');

        return response()->json([
            'message' => $message,
            'status' => 'System message sent successfully'
        ], 201);
    }
}
