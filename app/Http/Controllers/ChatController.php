<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Zeigt alle Chats mit Teilnehmern und Nachrichten.
     */
    public function index()
    {
        // Lädt alle Chats mit zugehörigen Teilnehmern und deren User-Daten
        return Chat::with(['participants.user'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Zeigt einen bestimmten Chat mit allen Teilnehmern und Nachrichten.
     */
    public function show(Chat $chat)
    {
        return $chat->load([
            'participants.user',
            'messages.user',
        ]);
    }

    /**
     * Erstellt einen neuen Chat (public, private oder community).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:public,private,community',
            'name' => 'nullable|string|max:255',
            'participants' => 'nullable|array',
            'participants.*.user_id' => 'required_with:participants|exists:users,id',
            'participants.*.role' => 'nullable|in:member,admin,owner',
        ]);

        // Chat erstellen
        $chat = Chat::create([
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
        ]);

        // Falls Teilnehmer mitgesendet werden → hinzufügen
        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $participant) {
                ChatParticipant::create([
                    'chat_id' => $chat->id,
                    'user_id' => $participant['user_id'],
                    'role' => $participant['role'] ?? 'member',
                ]);
            }
        }

        return response()->json($chat->load('participants.user'), 201);
    }

    /**
     * Aktualisiert Chat-Informationen (z. B. Name oder Typ).
     */
    public function update(Request $request, Chat $chat)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|in:public,private,community',
        ]);

        $chat->update($data);

        return response()->json($chat, 200);
    }

    /**
     * Löscht einen Chat inkl. Teilnehmer & Nachrichten.
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();
        return response()->noContent();
    }
}
