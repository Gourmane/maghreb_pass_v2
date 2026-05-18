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

        foreach ($attractions as [$name, $descriptionFr, $descriptionEn, $city, $address, $category, $entryPrice, $openingHours, $photos]) {
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
                    'photos' => $photos,
                ],
            );
        }
    }
}
