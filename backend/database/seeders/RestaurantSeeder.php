<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            ['Rick s Cafe', 'Restaurant emblematique de Casablanca inspire du cinema et de la cuisine internationale.', 'Iconic Casablanca restaurant inspired by cinema and international cuisine.', 'Casablanca', '248 Boulevard Sour Jdid', 'Internationale', 'gastronomique', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['La Sqala', 'Cuisine marocaine dans un cadre historique pres de l ancienne medina.', 'Moroccan cuisine in a historic setting near the old medina.', 'Casablanca', 'Boulevard des Almohades', 'Marocaine', 'moyen', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['Nomad Marrakech', 'Adresse moderne avec terrasse et plats marocains revisites.', 'Modern spot with rooftop terrace and reimagined Moroccan dishes.', 'Marrakech', 'Rahba Kedima', 'Marocaine moderne', 'moyen', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG']],
            ['Al Fassia Gueliz', 'Institution marrakchie connue pour sa cuisine marocaine traditionnelle.', 'Marrakech institution known for traditional Moroccan cuisine.', 'Marrakech', 'Gueliz', 'Marocaine', 'gastronomique', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG']],
            ['Le Dhow', 'Restaurant sur bateau avec vue sur le Bouregreg a Rabat.', 'Boat restaurant with Bouregreg river views in Rabat.', 'Rabat', 'Quai de Bouregreg', 'Internationale', 'moyen', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['Ty Potes', 'Restaurant convivial de Rabat avec cuisine variee et ambiance de quartier.', 'Friendly Rabat restaurant with varied cuisine and neighborhood atmosphere.', 'Rabat', 'Agdal', 'Fusion', 'moyen', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['El Morocco Club', 'Restaurant chic dans la kasbah de Tanger.', 'Chic restaurant in Tangier s kasbah.', 'Tanger', 'Kasbah', 'Marocaine moderne', 'gastronomique', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['Le Saveur du Poisson', 'Adresse populaire de Tanger specialisee dans le poisson frais.', 'Popular Tangier address specializing in fresh fish.', 'Tanger', 'Centre-ville', 'Poisson', 'moyen', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['Pure Passion', 'Restaurant d Agadir connu pour poissons et fruits de mer.', 'Agadir restaurant known for fish and seafood.', 'Agadir', 'Marina Agadir', 'Fruits de mer', 'gastronomique', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
            ['Cafe Clock Fes', 'Lieu culturel et restaurant dans la medina de Fes.', 'Cultural venue and restaurant in the Fes medina.', 'Fes', 'Medina', 'Marocaine', 'budget', ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg']],
        ];

        $geo = [
            'Rick s Cafe' => [33.6034, -7.6196, 4.5, true],
            'La Sqala' => [33.6022, -7.6202, 4.4, true],
            'Nomad Marrakech' => [31.6305, -7.9875, 4.4, true],
            'Al Fassia Gueliz' => [31.6366, -8.0130, 4.6, true],
            'Le Dhow' => [34.0272, -6.8245, 4.2, false],
            'Ty Potes' => [34.0038, -6.8486, 4.3, false],
            'El Morocco Club' => [35.7898, -5.8137, 4.5, true],
            'Le Saveur du Poisson' => [35.7801, -5.8130, 4.4, false],
            'Pure Passion' => [30.4212, -9.6177, 4.5, true],
            'Cafe Clock Fes' => [34.0612, -4.9816, 4.3, true],
        ];

        foreach ($restaurants as [$name, $descriptionFr, $descriptionEn, $city, $address, $cuisineType, $priceRange, $photos]) {
            [$latitude, $longitude, $rating, $isFeatured] = $geo[$name];

            Restaurant::updateOrCreate(
                ['name' => $name],
                [
                    'description_fr' => $descriptionFr,
                    'description_en' => $descriptionEn,
                    'city' => $city,
                    'address' => $address,
                    'cuisine_type' => $cuisineType,
                    'price_range' => $priceRange,
                    'phone' => '+212 500 000 001',
                    'whatsapp' => '+212 600 000 001',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'map_url' => "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}#map=16/{$latitude}/{$longitude}",
                    'is_featured' => $isFeatured,
                    'rating' => $rating,
                    'opening_hours' => 'Lun-Dim : 09:00 - 23:00',
                    'image_url' => $photos[0],
                    'photos' => $photos,
                ],
            );
        }
    }
}
