<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    /**
     * Die Felder, die massenweise zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
    ];

    /**
     * Beziehungen
     */

    // Teilnehmer gehört zu einem Chat
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Teilnehmer gehört zu einem User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Casts für Datumsfelder
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Helper-Methoden für Rollen
     */

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Scope-Beispiele (optional)
     */

    // Nur Admins eines Chats abrufen
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Nur Owner abrufen
    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }
}
