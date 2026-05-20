<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description_fr',
        'description_en',
        'city',
        'district',
        'stars',
        'price_min',
        'price_max',
        'currency',
        'website_url',
        'phone',
        'email',
        'latitude',
        'longitude',
        'map_url',
        'is_featured',
        'rating',
        'amenities',
        'image_url',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'stars' => 'integer',
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
            'rating' => 'decimal:1',
            'amenities' => 'array',
            'photos' => 'array',
        ];
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(HotelReservation::class);
    }
}
