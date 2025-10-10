<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chat extends Model
{
    use HasFactory;

    /**
     * Massenweise zuweisbare Felder.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',        // z. B. 'community', 'private', 'public'
        'name',        // Anzeigename oder Titel
        'community_id' // optional, falls mit Community verknüpft
    ];

    /**
     * ----------------------------
     * Beziehungen
     * ----------------------------
     */

    /** 1:n → Chat hat viele Nachrichten */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /** 1:n → Chat hat viele Teilnehmer (als Model ChatParticipant) */
    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /** n:m → Chat gehört zu vielen Usern (Pivot: chat_participants) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot('role')     // z. B. 'Owner', 'Member'
            ->withTimestamps();
    }

    /**
     * ----------------------------
     * Helper-Methoden
     * ----------------------------
     */

    /** Prüft, ob der Chat öffentlich ist */
    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    /** Prüft, ob der Chat zu einer Community gehört */
    public function isCommunity(): bool
    {
        return $this->type === 'community';
    }

    /** Prüft, ob der Chat privat ist (1:1-Chat) */
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }

    /**
     * Gibt zurück, ob ein bestimmter User Teilnehmer dieses Chats ist.
     */
    public function hasUser(int $userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Fügt einen Benutzer hinzu (optional mit Rolle).
     */
    public function addUser(int $userId, string $role = 'member'): void
    {
        $this->users()->syncWithoutDetaching([
            $userId => ['role' => strtolower($role)],
        ]);
    }


    /**
     * Entfernt einen Benutzer aus dem Chat.
     */
    public function removeUser(int $userId): void
    {
        $this->users()->detach($userId);
    }

    /**
     * Gibt die letzte Nachricht im Chat zurück.
     */
    public function lastMessage(): ?ChatMessage
    {
        return $this->messages()->latest()->first();
    }
}
