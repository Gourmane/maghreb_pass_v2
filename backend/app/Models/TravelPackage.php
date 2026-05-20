<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelPackage extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'title_fr',
        'title_en',
        'description_fr',
        'description_en',
        'city',
        'price_min',
        'price_max',
        'currency',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_min' => 'decimal:2',
            'price_max' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class, 'package_id')
            ->orderBy('day_number')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
