<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title_fr' => 'Weekend match a Casablanca',
                'title_en' => 'Casablanca Match Weekend',
                'description_fr' => 'Un programme compact pour vivre un match a Casablanca avec hotel, table marocaine et visite culturelle.',
                'description_en' => 'A compact plan to enjoy a match in Casablanca with a hotel, Moroccan dining, and a cultural visit.',
                'city' => 'Casablanca',
                'price_min' => 1200,
                'price_max' => 2600,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg',
                'items' => [
                    ['hotel', 'Four Seasons Hotel Casablanca', 1],
                    ['custom', 'Arrivee et installation', 1, 'Check-in, repos et preparation pour le match.'],
                    ['match', 'Grand Stade Hassan II', 1],
                    ['restaurant', 'La Sqala', 1],
                    ['attraction', 'Mosquee Hassan II', 2],
                ],
            ],
            [
                'title_fr' => 'Echappee culturelle a Marrakech',
                'title_en' => 'Marrakech Culture Escape',
                'description_fr' => 'Deux jours a Marrakech entre hebergement premium, palais, jardin et cuisine locale.',
                'description_en' => 'Two days in Marrakech with premium stay, palaces, gardens, and local cuisine.',
                'city' => 'Marrakech',
                'price_min' => 1800,
                'price_max' => 4200,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG',
                'items' => [
                    ['hotel', 'La Mamounia', 1],
                    ['attraction', 'Jardin Majorelle', 1],
                    ['restaurant', 'Nomad Marrakech', 1],
                    ['custom', 'Balade nocturne', 2, 'Temps libre autour de la medina et des souks.'],
                ],
            ],
        ];

        foreach ($packages as $data) {
            $items = $data['items'];
            unset($data['items']);

            $package = TravelPackage::updateOrCreate(
                ['title_en' => $data['title_en']],
                array_merge($data, ['currency' => 'MAD', 'is_active' => true])
            );

            $package->items()->delete();

            foreach ($items as $index => $item) {
                [$type, $lookup, $day] = $item;
                $customDescription = $item[3] ?? null;

                $package->items()->create([
                    'item_type' => $type,
                    'item_id' => $type === 'custom' ? null : $this->resolveItemId($type, $lookup),
                    'custom_title' => $type === 'custom' ? $lookup : null,
                    'custom_description' => $customDescription,
                    'day_number' => $day,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    private function resolveItemId(string $type, string $name): ?int
    {
        return match ($type) {
            'hotel' => Hotel::where('name', $name)->value('id'),
            'restaurant' => Restaurant::where('name', $name)->value('id'),
            'attraction' => Attraction::where('name', $name)->value('id'),
            'match' => FootballMatch::where('stadium', $name)->value('id'),
            default => null,
        };
    }
}
