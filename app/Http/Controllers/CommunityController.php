<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    /**
     * Zeigt alle Communities mit ihren Mitgliedern.
     */
    public function index()
    {
        return Community::with('users:id,name,email')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Zeigt eine bestimmte Community mit ihren Mitgliedern.
     */
    public function show(Community $community)
    {
        return $community->load('users:id,name,email');
    }

    /**
     * Erstellt eine neue Community + zugehörigen Chat.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'users' => 'nullable|array',
            'users.*.id' => 'required_with:users|exists:users,id',
            'users.*.role' => 'nullable|in:Owner,Admin,Member',
        ]);

        // 🔹 Neue Community anlegen
        $community = Community::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
        ]);

        // 🔹 Aktuellen User als Owner hinzufügen
        $userId = auth()->id();
        $community->users()->attach($userId, ['role' => 'Owner']);

        // 🔹 Optionale weitere User (Admins / Member)
        if (!empty($data['users'])) {
            foreach ($data['users'] as $user) {
                $community->users()->syncWithoutDetaching([
                    $user['id'] => ['role' => $user['role'] ?? 'Member'],
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 💬 Passenden Community-Chat automatisch anlegen
        |--------------------------------------------------------------------------
        */
        $chat = Chat::firstOrCreate(
            [
                'type' => 'community',
                'community_id' => $community->id,
            ],
            [
                'name' => 'Chat – ' . $community->name,
            ]
        );

        // Ersteller automatisch als Chat-Owner
        $chat->addUser($userId, 'owner');

        return redirect()->route('communitys')->with('success', 'Community und Chat erstellt.');
    }

    /**
     * Ändert die Rolle eines Benutzers in einer Community.
     */
    public function updateUserRole(Request $request, Community $community, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:Owner,Admin,Member',
        ]);

        DB::table('community_user')
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->update(['role' => $data['role']]);

        return response()->json(['message' => 'Rolle aktualisiert']);
    }

    /**
     * Löscht eine Community + zugehörigen Chat.
     */
    public function destroy(Community $community)
    {
        // 🗑️ Passenden Community-Chat löschen
        Chat::where('community_id', $community->id)->delete();

        $community->delete();
        return response()->noContent();
    }

    /**
     * Aktuellen Benutzer einer Community hinzufügen (+ Chatbeitritt).
     */
    public function join(Community $community)
    {
        $userId = auth()->id();

        // Verhindert doppelte Einträge
        $community->users()->syncWithoutDetaching([
            $userId => ['role' => 'Member'],
        ]);

        // 💬 Automatisch auch dem Community-Chat hinzufügen
        $chat = Chat::where('type', 'community')
            ->where('community_id', $community->id)
            ->first();

        if ($chat && !$chat->hasUser($userId)) {
            $chat->addUser($userId);
        }

        return back()->with('success', 'Community beigetreten.');
    }

    /**
     * Aktuellen Benutzer aus Community (und Chat) entfernen.
     */
    public function leave(Community $community)
    {
        $userId = auth()->id();

        // Benutzer aus der Community entfernen
        $community->users()->detach($userId);

        // 💬 Auch aus dem Community-Chat entfernen
        $chat = Chat::where('type', 'community')
            ->where('community_id', $community->id)
            ->first();

        if ($chat) {
            $chat->removeUser($userId);

            // Falls Chat keine User mehr hat → löschen
            if ($chat->users()->count() === 0) {
                $chat->delete();
            }
        }

        // Wenn keine User mehr in der Community → löschen
        if ($community->users()->count() === 0) {
            $community->delete();
        }

        return back()->with('success', 'Community verlassen.');
    }
}
