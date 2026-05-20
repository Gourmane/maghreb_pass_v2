<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'full_name',
        'email',
        'phone',
        'reservation_date',
        'reservation_time',
        'guests',
        'message',
        'status',
        'payment_status',
        'paid_at',
        'payment_reference',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'guests' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
