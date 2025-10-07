<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    /**
     * Die Felder, die massenweise zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
    ];

    /**
     * Beziehungen
     */

    // 1:n → Chat hat viele Nachrichten
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    // 1:n → Chat hat viele Teilnehmer (eigenes Model)
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    // n:m → Chat gehört zu vielen Usern über die Pivot-Tabelle chat_participants
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Helper-Methoden (optional, aber praktisch)
     */

    // Prüft, ob der Chat ein öffentlicher ist
    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    // Prüft, ob der Chat eine Community ist
    public function isCommunity(): bool
    {
        return $this->type === 'community';
    }

    // Prüft, ob der Chat ein privater Chat ist
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }
}
