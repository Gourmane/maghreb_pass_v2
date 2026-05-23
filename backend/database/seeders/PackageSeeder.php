<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title_fr' => 'Week-end Match à Casablanca',
                'title_en' => 'Casablanca Match Weekend',
                'description_fr' => 'Un week-end prêt pour supporter le Maroc, dormir au centre de Casablanca et découvrir les incontournables de la ville.',
                'description_en' => 'A ready-made weekend to support Morocco, stay in central Casablanca, and discover the city highlights.',
                'city' => 'Casablanca',
                'price_min' => 1600,
                'price_max' => 3000,
                'image_source' => ['type' => 'attraction', 'name' => 'Mosquee Hassan II'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'Barcelo Anfa Casablanca', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Ancienne Medina de Casablanca', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'La Sqala', 'day' => 1],
                    ['type' => 'match', 'team_home' => 'Maroc', 'team_away' => 'Portugal', 'match_date' => '2030-06-14', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Mosquee Hassan II', 'day' => 2],
                ],
            ],
            [
                'title_fr' => 'Découverte Marrakech & Match',
                'title_en' => 'Marrakech Culture Escape',
                'description_fr' => 'Deux jours pour combiner match international, jardin iconique, palais historique et cuisine marocaine moderne.',
                'description_en' => 'Two days combining an international match, iconic garden, historic palace, and modern Moroccan cuisine.',
                'city' => 'Marrakech',
                'price_min' => 2200,
                'price_max' => 4300,
                'image_source' => ['type' => 'attraction', 'name' => 'Jardin Majorelle'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'Movenpick Mansour Eddahbi Marrakech', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Jardin Majorelle', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'Nomad Marrakech', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Palais Bahia', 'day' => 2],
                    ['type' => 'match', 'team_home' => 'Brésil', 'team_away' => 'Sénégal', 'match_date' => '2030-06-17', 'day' => 2],
                ],
            ],
            [
                'title_fr' => 'Pack Budget Supporter Rabat',
                'title_en' => 'Rabat Budget Supporter Pack',
                'description_fr' => 'Un séjour accessible à Rabat avec hôtel pratique, monuments gratuits, restaurant convivial et grand match européen.',
                'description_en' => 'An accessible Rabat stay with a practical hotel, free monuments, friendly dining, and a major European match.',
                'city' => 'Rabat',
                'price_min' => 1200,
                'price_max' => 2200,
                'image_source' => ['type' => 'attraction', 'name' => 'Tour Hassan'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'ONOMO Hotel Rabat Terminus', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Tour Hassan', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'Ty Potes', 'day' => 1],
                    ['type' => 'match', 'team_home' => 'Espagne', 'team_away' => 'France', 'match_date' => '2030-06-15', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Kasbah des Oudayas', 'day' => 2],
                ],
            ],
            [
                'title_fr' => 'Expérience Match & Mer à Tanger',
                'title_en' => 'Tangier Match and Sea Experience',
                'description_fr' => 'Une expérience à Tanger entre match au Grand Stade, poissons frais et panoramas du Cap Spartel.',
                'description_en' => 'A Tangier experience with a Grand Stadium match, fresh fish, and Cap Spartel coastal views.',
                'city' => 'Tanger',
                'price_min' => 1350,
                'price_max' => 2500,
                'image_source' => ['type' => 'attraction', 'name' => 'Cap Spartel'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'Marina Bay City Center Tangier', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'Le Saveur du Poisson', 'day' => 1],
                    ['type' => 'match', 'team_home' => 'Argentine', 'team_away' => 'Croatie', 'match_date' => '2030-06-16', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Grottes d Hercule', 'day' => 2],
                    ['type' => 'attraction', 'name' => 'Cap Spartel', 'day' => 2],
                ],
            ],
            [
                'title_fr' => 'Découverte Authentique de Fès',
                'title_en' => 'Authentic Fes Discovery',
                'description_fr' => 'Un court séjour culturel dans la médina de Fès avec maison de charme et adresse locale budget.',
                'description_en' => 'A short cultural stay in the Fes medina with a charming hotel and a budget local address.',
                'city' => 'Fes',
                'price_min' => 1900,
                'price_max' => 3800,
                'image_source' => ['type' => 'attraction', 'name' => 'Medina de Fes'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'Palais Faraj Suites & Spa', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'Cafe Clock Fes', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Medina de Fes', 'day' => 2],
                ],
            ],
            [
                'title_fr' => 'Séjour Famille Plage à Agadir',
                'title_en' => 'Agadir Family Beach Stay',
                'description_fr' => 'Un pack détente pour familles avec resort en bord de mer, plage urbaine et restaurant de fruits de mer.',
                'description_en' => 'A relaxed family pack with a beachfront resort, city beach, and seafood restaurant.',
                'city' => 'Agadir',
                'price_min' => 1900,
                'price_max' => 3600,
                'image_source' => ['type' => 'attraction', 'name' => 'Plage d Agadir'],
                'items' => [
                    ['type' => 'hotel', 'name' => 'Hotel Riu Tikida Beach', 'day' => 1],
                    ['type' => 'attraction', 'name' => 'Plage d Agadir', 'day' => 1],
                    ['type' => 'restaurant', 'name' => 'Pure Passion', 'day' => 1],
                ],
            ],
        ];

        $hasLegacyTitleColumn = Schema::hasColumn('packages', 'title');

        foreach ($packages as $data) {
            $items = $data['items'];
            $imageSource = $data['image_source'];
            unset($data['items'], $data['image_source']);

            $data['image_url'] = $this->resolveImageUrl($imageSource);
            if ($hasLegacyTitleColumn) {
                $data['title'] = $data['title_fr'];
            }

            $package = TravelPackage::updateOrCreate(
                ['title_en' => $data['title_en']],
                array_merge($data, ['currency' => 'MAD', 'is_active' => true])
            );

            $package->items()->delete();

            foreach ($items as $index => $item) {
                $package->items()->create([
                    'item_type' => $item['type'],
                    'item_id' => $this->resolveItemId($item),
                    'custom_title' => null,
                    'custom_description' => null,
                    'day_number' => $item['day'],
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }

    private function resolveItemId(array $item): int
    {
        $record = $this->resolveCatalogRecord($item);

        if (! $record) {
            throw new RuntimeException('PackageSeeder missing '.$this->describeItem($item).'.');
        }

        return (int) $record->getKey();
    }

    private function resolveImageUrl(array $item): ?string
    {
        return $this->resolveCatalogRecord($item)?->image_url;
    }

    private function resolveCatalogRecord(array $item)
    {
        return match ($item['type']) {
            'hotel' => Hotel::where('name', $item['name'])->first(),
            'restaurant' => Restaurant::where('name', $item['name'])->first(),
            'attraction' => Attraction::where('name', $item['name'])->first(),
            'match' => FootballMatch::query()
                ->whereIn('team_home', $this->teamCandidates($item['team_home']))
                ->whereIn('team_away', $this->teamCandidates($item['team_away']))
                ->whereDate('match_date', $item['match_date'])
                ->first(),
            default => null,
        };
    }

    private function teamCandidates(string $name): array
    {
        return match ($name) {
            'Brésil' => ['Brésil', 'BrÃ©sil'],
            'Sénégal' => ['Sénégal', 'SÃ©nÃ©gal'],
            default => [$name],
        };
    }

    private function describeItem(array $item): string
    {
        if ($item['type'] === 'match') {
            return "match {$item['team_home']} vs {$item['team_away']} on {$item['match_date']}";
        }

        return "{$item['type']} {$item['name']}";
    }
}
