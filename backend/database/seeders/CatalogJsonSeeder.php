<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use RuntimeException;

class CatalogJsonSeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->loadCatalogData();

        $this->seedMatches($data['matches'] ?? []);
        $this->seedHotels($data['hotels'] ?? []);
        $this->seedRestaurants($data['restaurants'] ?? []);
        $this->seedAttractions($data['attractions'] ?? []);
    }

    private function loadCatalogData(): array
    {
        $path = base_path('../database_export/maghrebpass_data_export.json');

        if (! file_exists($path)) {
            throw new RuntimeException("Catalog export JSON was not found at {$path}");
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data)) {
            throw new RuntimeException('Catalog export JSON is not valid.');
        }

        return $data;
    }

    private function seedMatches(array $matches): void
    {
        $stadiums = [
            'Grand Stade Hassan II' => [33.5248, -7.6501],
            'Stade Moulay Abdellah' => [33.9581, -6.8896],
            'Grand Stade de Tanger' => [35.7419, -5.8584],
            'Grand Stade de Marrakech' => [31.7069, -7.9809],
            'Stade de Marrakech' => [31.7069, -7.9809],
        ];

        foreach ($matches as $match) {
            $stadium = (string) ($match['stadium'] ?? '');
            [$latitude, $longitude] = $stadiums[$stadium] ?? [null, null];

            FootballMatch::updateOrCreate(
                [
                    'team_home' => $match['team_home'],
                    'team_away' => $match['team_away'],
                    'match_date' => $match['match_date'],
                ],
                [
                    'team_home_code' => $match['team_home_code'] ?? null,
                    'team_home_flag_url' => $match['team_home_flag_url'] ?? null,
                    'team_away_code' => $match['team_away_code'] ?? null,
                    'team_away_flag_url' => $match['team_away_flag_url'] ?? null,
                    'score_home' => $match['score_home'] ?? null,
                    'score_away' => $match['score_away'] ?? null,
                    'match_time' => $this->normalizeTime($match['match_time'] ?? '00:00'),
                    'stadium' => $stadium,
                    'stadium_latitude' => $latitude,
                    'stadium_longitude' => $longitude,
                    'map_url' => $this->mapUrl($latitude, $longitude),
                    'city' => $match['city'],
                    'group_name' => $match['group_name'] ?? null,
                    'phase' => $match['phase'] ?? 'group',
                    'status' => $match['status'] ?? 'upcoming',
                ],
            );
        }
    }

    private function seedHotels(array $hotels): void
    {
        $fallbackPhotos = [
            'Four Seasons Hotel Casablanca' => ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg'],
            'Barcelo Anfa Casablanca' => ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg'],
            'La Mamounia' => ['https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG'],
            'Movenpick Mansour Eddahbi Marrakech' => ['https://upload.wikimedia.org/wikipedia/commons/b/b1/Marrakech_Menara.jpg'],
            'Sofitel Rabat Jardin des Roses' => ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg'],
            'ONOMO Hotel Rabat Terminus' => ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg'],
            'Hilton Tangier City Center' => ['https://upload.wikimedia.org/wikipedia/commons/3/35/Chefchaouen_Morocco.jpg'],
            'Marina Bay City Center Tangier' => ['https://upload.wikimedia.org/wikipedia/commons/3/35/Chefchaouen_Morocco.jpg'],
            'Hotel Riu Tikida Beach' => ['https://upload.wikimedia.org/wikipedia/commons/2/24/Ouzoud_Waterfalls_Morocco.jpg'],
            'Palais Faraj Suites & Spa' => ['https://upload.wikimedia.org/wikipedia/commons/1/1e/A%C3%AFt_Benhaddou.jpg'],
        ];

        $geo = [
            'Four Seasons Hotel Casablanca' => [33.5983, -7.6642, 4.8, true],
            'Barcelo Anfa Casablanca' => [33.5907, -7.6338, 4.5, true],
            'La Mamounia' => [31.6216, -7.9972, 4.9, true],
            'Movenpick Mansour Eddahbi Marrakech' => [31.6243, -8.0167, 4.4, false],
            'Sofitel Rabat Jardin des Roses' => [33.9904, -6.8468, 4.6, true],
            'ONOMO Hotel Rabat Terminus' => [34.0178, -6.8359, 4.1, false],
            'Hilton Tangier City Center' => [35.7673, -5.8016, 4.5, true],
            'Marina Bay City Center Tangier' => [35.7797, -5.8125, 4.2, false],
            'Hotel Riu Tikida Beach' => [30.4022, -9.6017, 4.3, true],
            'Palais Faraj Suites & Spa' => [34.0576, -4.9781, 4.7, true],
        ];

        foreach ($hotels as $hotel) {
            $photos = $this->directPhotos(
                $this->normalizePhotos($hotel['photos'] ?? []),
                $fallbackPhotos[$hotel['name']] ?? []
            );
            [$latitude, $longitude, $rating, $isFeatured] = $geo[$hotel['name']] ?? [null, null, null, false];

            Hotel::updateOrCreate(
                ['name' => $hotel['name']],
                [
                    'description_fr' => $hotel['description_fr'],
                    'description_en' => $hotel['description_en'],
                    'city' => $hotel['city'],
                    'district' => $hotel['district'] ?? null,
                    'stars' => $hotel['stars'],
                    'price_min' => $hotel['price_min'],
                    'price_max' => $hotel['price_max'],
                    'currency' => $hotel['currency'] ?? 'MAD',
                    'website_url' => $hotel['website_url'] ?? null,
                    'phone' => $hotel['phone'] ?? null,
                    'email' => $hotel['email'] ?? null,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'map_url' => $this->mapUrl($latitude, $longitude),
                    'is_featured' => $isFeatured,
                    'rating' => $rating,
                    'amenities' => ['Wi-Fi', 'Climatisation', 'Restaurant', 'Reception 24h/24'],
                    'image_url' => $photos[0] ?? null,
                    'photos' => $photos,
                ],
            );
        }
    }

    private function seedRestaurants(array $restaurants): void
    {
        $fallbackPhotos = [
            'Rick s Cafe' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'La Sqala' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Nomad Marrakech' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Al Fassia Gueliz' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Le Dhow' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Ty Potes' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'El Morocco Club' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Le Saveur du Poisson' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Pure Passion' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
            'Cafe Clock Fes' => ['https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg'],
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

        foreach ($restaurants as $restaurant) {
            $photos = $this->directPhotos(
                $this->normalizePhotos($restaurant['photos'] ?? []),
                $fallbackPhotos[$restaurant['name']] ?? []
            );
            [$latitude, $longitude, $rating, $isFeatured] = $geo[$restaurant['name']] ?? [null, null, null, false];

            Restaurant::updateOrCreate(
                ['name' => $restaurant['name']],
                [
                    'description_fr' => $restaurant['description_fr'],
                    'description_en' => $restaurant['description_en'],
                    'city' => $restaurant['city'],
                    'address' => $restaurant['address'] ?? null,
                    'cuisine_type' => $restaurant['cuisine_type'],
                    'price_range' => $restaurant['price_range'],
                    'phone' => $restaurant['phone'] ?? null,
                    'whatsapp' => $restaurant['whatsapp'] ?? null,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'map_url' => $this->mapUrl($latitude, $longitude),
                    'is_featured' => $isFeatured,
                    'rating' => $rating,
                    'opening_hours' => 'Lun-Dim : 09:00 - 23:00',
                    'image_url' => $photos[0] ?? null,
                    'photos' => $photos,
                ],
            );
        }
    }

    private function seedAttractions(array $attractions): void
    {
        $fallbackPhotos = [
            'Mosquee Hassan II' => ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg'],
            'Ancienne Medina de Casablanca' => ['https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg'],
            'Jardin Majorelle' => ['https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG'],
            'Palais Bahia' => ['https://upload.wikimedia.org/wikipedia/commons/8/8a/Bahia_Palace_Marrakech_Front_Courtyard_2009_LL.JPG'],
            'Kasbah des Oudayas' => ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg'],
            'Tour Hassan' => ['https://upload.wikimedia.org/wikipedia/commons/a/ae/Kasbah_of_the_Udayas_-_rabat.jpg'],
            'Grottes d Hercule' => ['https://upload.wikimedia.org/wikipedia/commons/6/6b/Caves_of_Hercules.jpg'],
            'Cap Spartel' => ['https://upload.wikimedia.org/wikipedia/commons/6/6b/Caves_of_Hercules.jpg'],
            'Plage d Agadir' => ['https://upload.wikimedia.org/wikipedia/commons/2/24/Ouzoud_Waterfalls_Morocco.jpg'],
            'Medina de Fes' => ['https://upload.wikimedia.org/wikipedia/commons/1/1e/A%C3%AFt_Benhaddou.jpg'],
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

        foreach ($attractions as $attraction) {
            $photos = $this->directPhotos(
                $this->normalizePhotos($attraction['photos'] ?? []),
                $fallbackPhotos[$attraction['name']] ?? []
            );
            [$latitude, $longitude, $rating, $isFeatured, $duration] = $geo[$attraction['name']] ?? [null, null, null, false, null];

            Attraction::updateOrCreate(
                ['name' => $attraction['name']],
                [
                    'description_fr' => $attraction['description_fr'],
                    'description_en' => $attraction['description_en'],
                    'city' => $attraction['city'],
                    'address' => $attraction['address'] ?? null,
                    'category' => $attraction['category'],
                    'entry_price' => $attraction['entry_price'] ?? null,
                    'opening_hours' => $attraction['opening_hours'] ?? null,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'map_url' => $this->mapUrl($latitude, $longitude),
                    'is_featured' => $isFeatured,
                    'rating' => $rating,
                    'recommended_duration_minutes' => $duration,
                    'image_url' => $photos[0] ?? null,
                    'photos' => $photos,
                ],
            );
        }
    }

    private function normalizePhotos(mixed $photos): array
    {
        if (is_string($photos)) {
            $decoded = json_decode($photos, true);

            return is_array($decoded) ? array_values(array_filter($decoded)) : [];
        }

        return is_array($photos) ? array_values(array_filter($photos)) : [];
    }

    private function directPhotos(array $photos, array $fallbackPhotos): array
    {
        $directPhotos = array_values(array_filter($photos, fn (string $url): bool => $this->isAcceptedDirectImageUrl($url)));

        return $directPhotos !== [] ? $directPhotos : $fallbackPhotos;
    }

    private function isAcceptedDirectImageUrl(string $url): bool
    {
        return preg_match('#^https://(upload\.wikimedia\.org/.+\.(?:jpg|jpeg|png|svg)|flagcdn\.com/.+\.png)$#i', $url) === 1;
    }

    private function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
    }

    private function mapUrl(?float $latitude, ?float $longitude): ?string
    {
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}#map=16/{$latitude}/{$longitude}";
    }
}
