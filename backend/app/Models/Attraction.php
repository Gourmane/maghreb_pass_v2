<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Attraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description_fr',
        'description_en',
        'city',
        'address',
        'category',
        'entry_price',
        'opening_hours',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'entry_price' => 'decimal:2',
            'photos' => 'array',
        ];
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }
}
