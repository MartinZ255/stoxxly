<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
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
        'message',
    ];

    /**
     * Beziehungen
     */

    // Jede Nachricht gehört zu einem Chat
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // Jede Nachricht wurde von einem User gesendet
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Casting
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
}
