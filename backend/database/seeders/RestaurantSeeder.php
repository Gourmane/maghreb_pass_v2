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

        foreach ($restaurants as [$name, $descriptionFr, $descriptionEn, $city, $address, $cuisineType, $priceRange, $photos]) {
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
                    'photos' => $photos,
                ],
            );
        }
    }
}
