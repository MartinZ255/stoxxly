<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    use HasFactory;

    /**
     * Massenweise zuweisbare Felder.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'user_id',
        'role',
    ];

    /**
     * ----------------------------
     * Beziehungen
     * ----------------------------
     */

    /** Teilnehmer gehört zu einem Chat. */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /** Teilnehmer gehört zu einem Benutzer. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ----------------------------
     * Casts
     * ----------------------------
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ----------------------------
     * Rollen-Helper
     * ----------------------------
     */

    public function isOwner(): bool
    {
        return strtolower($this->role) === 'owner';
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    public function isMember(): bool
    {
        return strtolower($this->role) === 'member';
    }

    /** Rolle prüfen (z. B. $participant->hasRole('admin')) */
    public function hasRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    /** Rolle neu zuweisen */
    public function assignRole(string $role): self
    {
        $this->role = strtolower($role);
        $this->save();

        return $this;
    }

    /** Benutzer zum Admin befördern */
    public function promoteToAdmin(): self
    {
        return $this->assignRole('admin');
    }

    /** Benutzer auf Member herabstufen */
    public function demoteToMember(): self
    {
        return $this->assignRole('member');
    }

    /** Benutzer aus dem Chat entfernen */
    public function removeFromChat(): void
    {
        $this->delete();
    }

    /**
     * ----------------------------
     * Scopes
     * ----------------------------
     */

    /** Nur Admins eines Chats abrufen */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /** Nur Owner abrufen */
    public function scopeOwners($query)
    {
        return $query->where('role', 'owner');
    }

    /** Nur Members abrufen */
    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }
}
