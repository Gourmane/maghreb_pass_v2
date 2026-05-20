<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\HandlesPhotoUploads;
use App\Http\Controllers\Api\Admin\Concerns\ProtectsCatalogItemDeletion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RestaurantController extends Controller
{
    use HandlesPhotoUploads;
    use ProtectsCatalogItemDeletion;

    public function index(Request $request): AnonymousResourceCollection
    {
        return RestaurantResource::collection(
            Restaurant::query()
                ->orderBy('city')
                ->orderBy('name')
                ->paginate($this->perPage($request, 15))
        );
    }

    public function store(UpsertRestaurantRequest $request): RestaurantResource
    {
        return new RestaurantResource(Restaurant::create(
            $this->validatedDataWithPhotoUploads($request, 'uploads/restaurants')
        ));
    }

    public function show(Restaurant $restaurant): RestaurantResource
    {
        return new RestaurantResource($restaurant);
    }

    public function update(UpsertRestaurantRequest $request, Restaurant $restaurant): RestaurantResource
    {
        $restaurant->update($this->validatedDataWithPhotoUploads($request, 'uploads/restaurants'));

        return new RestaurantResource($restaurant);
    }

    public function destroy(Restaurant $restaurant): Response
    {
        $this->abortIfCatalogItemIsUsed('restaurant', $restaurant->id);

        $restaurant->delete();

        return response()->noContent();
    }
}
