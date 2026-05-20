<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'item_type',
        'item_id',
        'custom_title',
        'custom_description',
        'day_number',
        'start_time',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'day_number' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
