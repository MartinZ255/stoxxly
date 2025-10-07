<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\User;
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
     * Erstellt eine neue Community.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'nullable|array',
            'users.*.id' => 'required_with:users|exists:users,id',
            'users.*.role' => 'nullable|in:Owner,Admin,Member',
        ]);

        $community = Community::create([
            'name' => $data['name'],
        ]);

        // Falls User mitgesendet werden → zuordnen
        if (!empty($data['users'])) {
            foreach ($data['users'] as $user) {
                $community->users()->attach($user['id'], [
                    'role' => $user['role'] ?? 'Member',
                ]);
            }
        }

        return response()->json($community->load('users:id,name,email'), 201);
    }

    /**
     * Fügt einen Benutzer zur Community hinzu.
     */
    public function addUser(Request $request, Community $community)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|in:Owner,Admin,Member',
        ]);

        $community->users()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role'] ?? 'Member'],
        ]);

        return response()->json([
            'message' => 'User hinzugefügt',
            'community' => $community->load('users:id,name,email'),
        ]);
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
     * Entfernt einen Benutzer aus der Community.
     */
    public function removeUser(Community $community, User $user)
    {
        $community->users()->detach($user->id);

        return response()->json(['message' => 'User entfernt']);
    }

    /**
     * Löscht eine Community.
     */
    public function destroy(Community $community)
    {
        $community->delete();
        return response()->noContent();
    }
}
