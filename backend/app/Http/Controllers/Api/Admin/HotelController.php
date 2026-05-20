<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\HandlesPhotoUploads;
use App\Http\Controllers\Api\Admin\Concerns\ProtectsCatalogItemDeletion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertHotelRequest;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class HotelController extends Controller
{
    use HandlesPhotoUploads;
    use ProtectsCatalogItemDeletion;

    public function index(Request $request): AnonymousResourceCollection
    {
        return HotelResource::collection(
            Hotel::query()
                ->orderBy('city')
                ->orderBy('name')
                ->paginate($this->perPage($request, 15))
        );
    }

    public function store(UpsertHotelRequest $request): HotelResource
    {
        $data = $this->validatedDataWithPhotoUploads($request, 'uploads/hotels');
        $data['currency'] ??= 'MAD';

        return new HotelResource(Hotel::create($data));
    }

    public function show(Hotel $hotel): HotelResource
    {
        return new HotelResource($hotel);
    }

    public function update(UpsertHotelRequest $request, Hotel $hotel): HotelResource
    {
        $data = $this->validatedDataWithPhotoUploads($request, 'uploads/hotels');
        $data['currency'] ??= 'MAD';
        $hotel->update($data);

        return new HotelResource($hotel);
    }

    public function destroy(Hotel $hotel): Response
    {
        $this->abortIfCatalogItemIsUsed('hotel', $hotel->id);

        $hotel->delete();

        return response()->noContent();
    }
}
