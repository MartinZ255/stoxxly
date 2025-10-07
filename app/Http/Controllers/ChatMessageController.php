<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    /**
     * Zeigt alle Nachrichten eines bestimmten Chats.
     */
    public function index($chatId)
    {
        $chat = Chat::findOrFail($chatId);

        // Lädt Nachrichten mit Benutzerinfos, neueste zuerst
        return ChatMessage::where('chat_id', $chat->id)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Erstellt eine neue Nachricht in einem Chat.
     */
    public function store(Request $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $data['chat_id'] = $chat->id;

        $message = ChatMessage::create($data);

        // Lädt Userdaten direkt mit
        return response()->json($message->load('user:id,name,email'), 201);
    }

    /**
     * Optional: Eine einzelne Nachricht anzeigen.
     */
    public function show(ChatMessage $message)
    {
        return $message->load(['user:id,name,email', 'chat']);
    }

    /**
     * Optional: Eine Nachricht löschen.
     */
    public function destroy(ChatMessage $message)
    {
        $message->delete();
        return response()->noContent();
    }
}
