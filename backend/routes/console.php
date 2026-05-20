<?php

use App\Models\Attraction;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('catalog:sync-image-seeds', function () {
    $path = base_path('../database_export/maghrebpass_data_export.json');

    if (! file_exists($path)) {
        $this->error("Catalog export JSON was not found at {$path}");

        return self::FAILURE;
    }

    $data = json_decode((string) file_get_contents($path), true);

    if (! is_array($data)) {
        $this->error('Catalog export JSON is not valid.');

        return self::FAILURE;
    }

    $syncCount = 0;
    $syncCount += syncCatalogImages($data, 'hotels', Hotel::query()->get(['id', 'name', 'image_url', 'photos']));
    $syncCount += syncCatalogImages($data, 'restaurants', Restaurant::query()->get(['id', 'name', 'image_url', 'photos']));
    $syncCount += syncCatalogImages($data, 'attractions', Attraction::query()->get(['id', 'name', 'image_url', 'photos']));

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        $this->error('Unable to encode catalog export JSON.');

        return self::FAILURE;
    }

    file_put_contents($path, $json.PHP_EOL);

    $this->info("Synced {$syncCount} catalog image records into {$path}");

    return self::SUCCESS;
})->purpose('Copy current hotel, restaurant, and attraction image URLs into the catalog seed export');

if (! function_exists('syncCatalogImages')) {
    function syncCatalogImages(array &$data, string $section, iterable $records): int
    {
        if (! isset($data[$section]) || ! is_array($data[$section])) {
            return 0;
        }

        $byId = [];
        $byName = [];

        foreach ($records as $record) {
            $byId[(string) $record->id] = $record;
            $byName[$record->name] = $record;
        }

        $count = 0;

        foreach ($data[$section] as &$item) {
            $record = $byId[(string) ($item['id'] ?? '')] ?? $byName[$item['name'] ?? ''] ?? null;

            if (! $record) {
                continue;
            }

            $photos = normalizeCatalogSeedPhotos($record->photos);

            if ($record->image_url && ! in_array($record->image_url, $photos, true)) {
                array_unshift($photos, $record->image_url);
            }

            $item['image_url'] = $record->image_url;
            $item['photos'] = json_encode(array_values(array_unique($photos)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $count++;
        }

        unset($item);

        return $count;
    }
}

if (! function_exists('normalizeCatalogSeedPhotos')) {
    function normalizeCatalogSeedPhotos(mixed $photos): array
    {
        if (is_string($photos)) {
            $decoded = json_decode($photos, true);

            return is_array($decoded) ? array_values(array_filter($decoded)) : [];
        }

        return is_array($photos) ? array_values(array_filter($photos)) : [];
    }
}
