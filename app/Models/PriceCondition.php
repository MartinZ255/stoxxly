<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_notification_id',
        'indicator_id',
        'operator',
        'value',
    ];
}
