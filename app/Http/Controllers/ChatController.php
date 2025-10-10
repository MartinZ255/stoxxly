<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Zeigt alle Chats des eingeloggten Users (inkl. Teilnehmer & letzter Nachricht).
     */
    public function index()
    {
        $userId = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | 🌐 Öffentlichen Chat immer bereitstellen
        |--------------------------------------------------------------------------
        */
        $publicChat = Chat::firstOrCreate(
            ['type' => 'public'],
            ['name' => 'Öffentlicher Chat']
        );

        // Nutzer automatisch hinzufügen, falls noch kein Teilnehmer
        if (!$publicChat->hasUser($userId)) {
            $publicChat->addUser($userId);
        }

        $publicChat->load(['participants.user:id,name', 'messages.user:id,name']);
        $publicChat->messages_count = $publicChat->messages()->count();

        /*
        |--------------------------------------------------------------------------
        | 🏘️ Community-Chats aller beigetretenen Communitys laden
        |--------------------------------------------------------------------------
        */
        $communityIds = \App\Models\Community::whereHas('users', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        $communityChats = Chat::where('type', 'community')
            ->whereIn('community_id', $communityIds)
            ->with(['participants.user:id,name', 'messages.user:id,name'])
            ->withCount('messages')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 💬 Private Chats des Users (ohne Community)
        |--------------------------------------------------------------------------
        */
        $privateChats = Chat::whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->where(function ($q) {
                $q->whereNull('community_id')->orWhere('type', 'private');
            })
            ->where('type', '!=', 'public')
            ->with(['participants.user:id,name', 'messages.user:id,name'])
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 🧩 Zusammenführen & sortieren
        |--------------------------------------------------------------------------
        */
        $chats = collect([$publicChat])
            ->merge($communityChats)
            ->merge($privateChats)
            ->values();

        return response()->json($chats);
    }



    /**
     * Zeigt einen bestimmten Chat mit Teilnehmern & Nachrichten.
     */
    public function show(Chat $chat)
    {
        // Optional: Zugriffsschutz – nur Teilnehmer dürfen sehen
        if (!$chat->hasUser(auth()->id())) {
            return response()->json(['message' => 'Zugriff verweigert'], 403);
        }

        $chat->load([
            'participants.user:id,name',
            'messages.user:id,name',
        ]);

        return response()->json($chat);
    }

    /**
     * Erstellt einen neuen Chat (z. B. private, community, public).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:public,private,community',
            'name' => 'nullable|string|max:255',
            'community_id' => 'nullable|exists:communities,id',
            'participants' => 'nullable|array',
            'participants.*.user_id' => 'required_with:participants|exists:users,id',
            'participants.*.role' => 'nullable|in:member,admin,owner',
        ]);

        return DB::transaction(function () use ($data) {
            // Chat erstellen
            $chat = Chat::create([
                'type' => $data['type'],
                'name' => $data['name'] ?? null,
                'community_id' => $data['community_id'] ?? null,
            ]);

            // Ersteller immer als Owner hinzufügen
            $chat->addUser(auth()->id(), 'owner');

            // Weitere Teilnehmer hinzufügen
            if (!empty($data['participants'])) {
                foreach ($data['participants'] as $p) {
                    $chat->addUser($p['user_id'], $p['role'] ?? 'member');
                }
            }

            return response()->json($chat->load('participants.user'), 201);
        });
    }

    /**
     * Aktualisiert Chat-Informationen (z. B. Name, Typ).
     */
    public function update(Request $request, Chat $chat)
    {
        $this->authorizeChatOwner($chat);

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|in:public,private,community',
        ]);

        $chat->update($data);

        return response()->json([
            'message' => 'Chat aktualisiert',
            'chat' => $chat,
        ], 200);
    }

    /**
     * Löscht einen Chat inkl. Teilnehmer & Nachrichten.
     */
    public function destroy(Chat $chat)
    {
        $this->authorizeChatOwner($chat);

        $chat->delete();

        return response()->json(['message' => 'Chat gelöscht'], 200);
    }

    /**
     * Fügt den aktuellen User einem Chat hinzu.
     */
    public function join(Chat $chat)
    {
        if ($chat->hasUser(auth()->id())) {
            return response()->json(['message' => 'Bereits Teilnehmer'], 200);
        }

        $chat->addUser(auth()->id());

        return response()->json(['message' => 'Chat beigetreten']);
    }

    /**
     * Entfernt den aktuellen User aus dem Chat.
     * Wenn danach keine Teilnehmer mehr übrig sind, wird der Chat gelöscht.
     */
    public function leave(Chat $chat)
    {
        $userId = auth()->id();

        $chat->removeUser($userId);

        if ($chat->users()->count() === 0) {
            $chat->delete();
            return response()->json(['message' => 'Chat gelöscht, da keine Teilnehmer mehr vorhanden.']);
        }

        return response()->json(['message' => 'Chat verlassen.']);
    }

    /**
     * Interne Helper-Methode zur Owner-Überprüfung.
     */
    protected function authorizeChatOwner(Chat $chat): void
    {
        $participant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$participant || !$participant->isOwner()) {
            abort(403, 'Nur der Chat-Owner darf diese Aktion durchführen.');
        }
    }

    /**
     * Speichert eine neue Nachricht in einem Chat.
     */
    public function storeMessage(Request $request, Chat $chat)
    {
        // Nur Teilnehmer dürfen schreiben
        if (!$chat->hasUser(auth()->id())) {
            return response()->json(['message' => 'Nicht erlaubt'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // Nachricht speichern
        $message = $chat->messages()->create([
            'user_id' => auth()->id(),
            'message' => $data['content'],
        ]);

        // User-Daten direkt mitladen
        $message->load('user:id,name');

        // 🔥 Antwortstruktur sicherstellen
        return response()->json([
            'id' => $message->id,
            'chat_id' => $chat->id,
            'user_id' => $message->user_id,
            'message' => $message->message,
            'created_at' => $message->created_at,
            'user' => $message->user,
        ], 201);
    }
}
