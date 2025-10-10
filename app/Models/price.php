<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'timestamp',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'currency',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        // 'decimal:10' sorgt dafür, dass Eloquent die Felder als strings mit 10 Dezimalstellen behandelt
        'open' => 'decimal:10',
        'high' => 'decimal:10',
        'low' => 'decimal:10',
        'close' => 'decimal:10',
        'volume' => 'decimal:10',
    ];

    /**
     * Relation: Price gehört zu einem Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

