<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

trait HandlesPhotoUploads
{
    private function validatedDataWithPhotoUploads(FormRequest $request, string $directory): array
    {
        $data = $request->validated();
        $existingPhotos = $data['photos'] ?? [];
        unset($data['photo_files']);

        if (! $request->hasFile('photo_files')) {
            return $data;
        }

        $uploadedPhotos = [];

        foreach ($request->file('photo_files') as $photo) {
            $path = $photo->store($directory, 'public');
            $uploadedPhotos[] = Storage::disk('public')->url($path);
        }

        $data['photos'] = array_values(array_merge($existingPhotos, $uploadedPhotos));

        return $data;
    }
}
