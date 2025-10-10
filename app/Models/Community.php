<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    /**
     * Die Spalten, die massenweise zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Spalten, die gecastet werden sollen.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'community_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    protected $appends = ['member_count'];

    public function getMemberCountAttribute()
    {
        return $this->users()->count();
    }

}
