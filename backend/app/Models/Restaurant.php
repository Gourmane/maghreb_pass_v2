<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description_fr',
        'description_en',
        'city',
        'address',
        'cuisine_type',
        'price_range',
        'phone',
        'whatsapp',
        'latitude',
        'longitude',
        'map_url',
        'is_featured',
        'rating',
        'opening_hours',
        'image_url',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
            'rating' => 'decimal:1',
            'photos' => 'array',
        ];
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(RestaurantReservation::class);
    }
}
