<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatTransferController extends Controller
{
    /**
     * Transfer chat to another user
     */
    public function transfer(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();

        // Check if user is admin of the chat
        $participant = $chat->participants()->where('user_id', $user->id)->first();
        if (!$participant || $participant->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $request->validate([
            'new_user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $newUser = \App\Models\User::find($request->new_user_id);

        // Check if new user belongs to the same organization
        if (!$newUser->organizations()->where('organization_id', $chat->organization_id)->exists()) {
            return response()->json(['message' => 'User does not belong to this organization'], 400);
        }

        // Check if new user is already a participant
        if ($chat->participants()->where('user_id', $newUser->id)->exists()) {
            return response()->json(['message' => 'User is already a participant'], 400);
        }

        // Add new user as admin
        ChatParticipant::create([
            'chat_id' => $chat->id,
            'user_id' => $newUser->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Update chat assignment
        $chat->update([
            'assigned_to' => $newUser->id,
            'status' => 'transferred',
        ]);

        // Send system message about transfer
        $transferMessage = $request->reason 
            ? "Чат передан пользователю {$newUser->name}. Причина: {$request->reason}"
            : "Чат передан пользователю {$newUser->name}";

        Message::create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'content' => $transferMessage,
            'type' => 'system',
        ]);

        return response()->json([
            'message' => 'Chat transferred successfully',
            'new_admin' => $newUser->name
        ]);
    }

    /**
     * Get transfer history for a chat
     */
    public function history(Request $request, Chat $chat): JsonResponse
    {
        $user = $request->user();

        // Check if user is participant
        if (!$chat->participants()->where('user_id', $user->id)->where('is_active', true)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $transferMessages = $chat->messages()
            ->where('type', 'system')
            ->where('content', 'like', '%передан%')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'transfer_history' => $transferMessages
        ]);
    }
}
