<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ChatMessage extends Model
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
        'message',
    ];

    /**
     * ----------------------------
     * Beziehungen
     * ----------------------------
     */

    /** Jede Nachricht gehört zu einem Chat. */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /** Jede Nachricht wurde von einem Benutzer gesendet. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ----------------------------
     * Attribute & Casts
     * ----------------------------
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Gibt das Erstellungsdatum als formatierten String zurück (z. B. "12:45" oder "Gestern").
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->format('H:i')
            : '';
    }

    /**
     * Optional: Für API- oder Inertia-Response als vereinfachtes Array.
     */
    public function toChatArray(): array
    {
        return [
            'id'         => $this->id,
            'message'    => $this->message,
            'user'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'time'       => $this->formatted_time,
        ];
    }

    /**
     * ----------------------------
     * Query Scopes
     * ----------------------------
     */

    /** Holt nur Nachrichten eines bestimmten Chats. */
    public function scopeForChat($query, int $chatId)
    {
        return $query->where('chat_id', $chatId);
    }

    /** Holt die neuesten Nachrichten zuerst. */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }
}
