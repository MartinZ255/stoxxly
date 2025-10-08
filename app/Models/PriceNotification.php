<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_channel',
        'is_active',
        'user_id',
        'asset_id',
    ];
}
