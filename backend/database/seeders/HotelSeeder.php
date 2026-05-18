<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            ['Four Seasons Hotel Casablanca', 'Hotel de luxe face a l ocean avec chambres modernes et service haut de gamme.', 'Luxury oceanfront hotel with modern rooms and premium service.', 'Casablanca', 'Anfa', 5, 2800, 5200, 'https://www.fourseasons.com/casablanca/', ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/0/06/The_Open_Area_of_Hassan_II_Mosque_-_Casablanca_Morocco.jpg']],
            ['Barcelo Anfa Casablanca', 'Adresse centrale proche des restaurants, commerces et quartiers d affaires.', 'Central address close to restaurants, shops and business districts.', 'Casablanca', 'Centre-ville', 5, 1100, 2200, 'https://www.barcelo.com/', ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/0/06/The_Open_Area_of_Hassan_II_Mosque_-_Casablanca_Morocco.jpg']],
            ['La Mamounia', 'Palace iconique de Marrakech avec jardins, spa et experience marocaine raffinee.', 'Iconic Marrakech palace with gardens, spa and refined Moroccan experience.', 'Marrakech', 'Hivernage', 5, 4500, 9000, 'https://www.mamounia.com/', ['https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG', 'https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG']],
            ['Movenpick Mansour Eddahbi Marrakech', 'Grand hotel adapte aux voyageurs qui veulent rester pres du centre et des evenements.', 'Large hotel suited for travelers who want to stay near the center and events.', 'Marrakech', 'Hivernage', 5, 1500, 3200, 'https://movenpick.accor.com/', ['https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG', 'https://upload.wikimedia.org/wikipedia/commons/b/b1/Marrakech_Menara.jpg']],
            ['Sofitel Rabat Jardin des Roses', 'Hotel elegant avec grands jardins, ideal pour decouvrir Rabat confortablement.', 'Elegant hotel with large gardens, ideal for discovering Rabat comfortably.', 'Rabat', 'Souissi', 5, 1800, 3600, 'https://all.accor.com/', ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Flag_of_Morocco.svg']],
            ['ONOMO Hotel Rabat Terminus', 'Hotel pratique en centre-ville, proche de la gare et des principaux axes.', 'Convenient city-center hotel near the train station and main roads.', 'Rabat', 'Centre-ville', 4, 800, 1500, 'https://www.onomohotel.com/', ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg', 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Flag_of_Morocco.svg']],
            ['Hilton Tangier City Center', 'Hotel moderne connecte au centre commercial et proche de la corniche.', 'Modern hotel connected to the mall and close to the corniche.', 'Tanger', 'City Center', 5, 1400, 2900, 'https://www.hilton.com/', ['https://upload.wikimedia.org/wikipedia/commons/3/35/Chefchaouen_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/9/9c/A_view_of_one_of_the_many_blue-painted_streets_in_Chefchaouen,_Morocco.jpg']],
            ['Marina Bay City Center Tangier', 'Hotel confortable face a la baie, pratique pour explorer Tanger.', 'Comfortable hotel facing the bay, convenient for exploring Tangier.', 'Tanger', 'Corniche', 4, 900, 1800, 'https://www.marinabaytangier.com/', ['https://upload.wikimedia.org/wikipedia/commons/3/35/Chefchaouen_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/9/9c/A_view_of_one_of_the_many_blue-painted_streets_in_Chefchaouen,_Morocco.jpg']],
            ['Hotel Riu Tikida Beach', 'Resort en bord de mer a Agadir avec ambiance detente et acces plage.', 'Beachfront resort in Agadir with relaxed atmosphere and beach access.', 'Agadir', 'Secteur touristique', 4, 1300, 2600, 'https://www.riu.com/', ['https://upload.wikimedia.org/wikipedia/commons/2/24/Ouzoud_Waterfalls_Morocco.jpg', 'https://upload.wikimedia.org/wikipedia/commons/1/1e/A%C3%AFt_Benhaddou.jpg']],
            ['Palais Faraj Suites & Spa', 'Maison d hotes de charme avec vues sur la medina de Fes.', 'Charming boutique hotel with views over the Fes medina.', 'Fes', 'Medina', 5, 1700, 3400, 'https://www.palaisfaraj.com/', ['https://upload.wikimedia.org/wikipedia/commons/1/1e/A%C3%AFt_Benhaddou.jpg', 'https://upload.wikimedia.org/wikipedia/commons/2/24/Ouzoud_Waterfalls_Morocco.jpg']],
        ];

        foreach ($hotels as [$name, $descriptionFr, $descriptionEn, $city, $district, $stars, $priceMin, $priceMax, $websiteUrl, $photos]) {
            Hotel::updateOrCreate(
                ['name' => $name],
                [
                    'description_fr' => $descriptionFr,
                    'description_en' => $descriptionEn,
                    'city' => $city,
                    'district' => $district,
                    'stars' => $stars,
                    'price_min' => $priceMin,
                    'price_max' => $priceMax,
                    'currency' => 'MAD',
                    'website_url' => $websiteUrl,
                    'phone' => '+212 500 000 000',
                    'email' => 'contact@example.test',
                    'photos' => $photos,
                ],
            );
        }
    }
}
