<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    public function run(): void
    {
        $attractions = [
            ['Mosquee Hassan II', 'Monument majeur de Casablanca construit face a l Atlantique.', 'Major Casablanca landmark built facing the Atlantic Ocean.', 'Casablanca', 'Boulevard Sidi Mohammed Ben Abdallah', 'Mosquee', 130, '09:00-18:00', ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/0/06/The_Open_Area_of_Hassan_II_Mosque_-_Casablanca_Morocco.jpg']],
            ['Ancienne Medina de Casablanca', 'Quartier historique avec ruelles, commerces et ambiance locale.', 'Historic district with alleys, shops and local atmosphere.', 'Casablanca', 'Ancienne Medina', 'Medina', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/0/06/The_Open_Area_of_Hassan_II_Mosque_-_Casablanca_Morocco.jpg']],
            ['Jardin Majorelle', 'Jardin botanique colore et musee emblematique de Marrakech.', 'Colorful botanical garden and iconic Marrakech museum.', 'Marrakech', 'Rue Yves Saint Laurent', 'Musee', 150, '08:00-18:30', ['https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG']],
            ['Palais Bahia', 'Palais historique connu pour ses patios et son artisanat decoratif.', 'Historic palace known for patios and decorative craftsmanship.', 'Marrakech', 'Medina', 'Patrimoine historique', 70, '09:00-17:00', ['https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG']],
            ['Kasbah des Oudayas', 'Site historique de Rabat avec ruelles bleues et vue sur l ocean.', 'Historic Rabat site with blue alleys and ocean views.', 'Rabat', 'Oudayas', 'Patrimoine historique', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg']],
            ['Tour Hassan', 'Monument historique et symbole architectural de Rabat.', 'Historic monument and architectural symbol of Rabat.', 'Rabat', 'Quartier Hassan', 'Patrimoine historique', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg']],
            ['Grottes d Hercule', 'Site naturel celebre pres de Tanger avec ouverture sur l ocean.', 'Famous natural site near Tangier with an opening to the ocean.', 'Tanger', 'Cap Spartel', 'Autre', 60, '10:00-18:00', ['https://upload.wikimedia.org/wikipedia/commons/6/6b/Caves_of_Hercules.jpg']],
            ['Cap Spartel', 'Point panoramique ou se rencontrent Atlantique et Mediterranee.', 'Scenic point where the Atlantic and Mediterranean meet.', 'Tanger', 'Cap Spartel', 'Plage', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/6/6b/Caves_of_Hercules.jpg']],
            ['Plage d Agadir', 'Longue plage urbaine ideale pour promenade et detente.', 'Long city beach ideal for walking and relaxing.', 'Agadir', 'Corniche', 'Plage', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/2/24/Ouzoud_Waterfalls_Morocco.jpg']],
            ['Medina de Fes', 'Medina classee au patrimoine mondial, connue pour ses souks et monuments.', 'World heritage medina known for souks and monuments.', 'Fes', 'Fes el Bali', 'Medina', 0, 'Toute la journee', ['https://upload.wikimedia.org/wikipedia/commons/1/1e/A%C3%AFt_Benhaddou.jpg']],
        ];

        $geo = [
            'Mosquee Hassan II' => [33.6084, -7.6326, 4.8, true, 90],
            'Ancienne Medina de Casablanca' => [33.5993, -7.6176, 4.1, false, 120],
            'Jardin Majorelle' => [31.6417, -8.0030, 4.7, true, 90],
            'Palais Bahia' => [31.6218, -7.9818, 4.6, true, 90],
            'Kasbah des Oudayas' => [34.0316, -6.8361, 4.7, true, 90],
            'Tour Hassan' => [34.0241, -6.8229, 4.6, true, 60],
            'Grottes d Hercule' => [35.7590, -5.9398, 4.4, true, 60],
            'Cap Spartel' => [35.7919, -5.9224, 4.5, false, 60],
            'Plage d Agadir' => [30.4142, -9.6067, 4.4, true, 120],
            'Medina de Fes' => [34.0625, -4.9830, 4.8, true, 180],
        ];

        foreach ($attractions as [$name, $descriptionFr, $descriptionEn, $city, $address, $category, $entryPrice, $openingHours, $photos]) {
            [$latitude, $longitude, $rating, $isFeatured, $duration] = $geo[$name];

            Attraction::updateOrCreate(
                ['name' => $name],
                [
                    'description_fr' => $descriptionFr,
                    'description_en' => $descriptionEn,
                    'city' => $city,
                    'address' => $address,
                    'category' => $category,
                    'entry_price' => $entryPrice,
                    'opening_hours' => $openingHours,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'map_url' => "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}#map=16/{$latitude}/{$longitude}",
                    'is_featured' => $isFeatured,
                    'rating' => $rating,
                    'recommended_duration_minutes' => $duration,
                    'image_url' => $photos[0],
                    'photos' => $photos,
                ],
            );
        }
    }
}
