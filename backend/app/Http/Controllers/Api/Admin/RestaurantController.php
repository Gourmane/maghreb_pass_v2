<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\HandlesPhotoUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\PackageItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RestaurantController extends Controller
{
    use HandlesPhotoUploads;

    public function index(Request $request): AnonymousResourceCollection
    {
        return RestaurantResource::collection(
            Restaurant::query()
                ->orderBy('city')
                ->orderBy('name')
                ->paginate($request->integer('per_page', 15))
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
        abort_if(
            PackageItem::where('item_type', 'restaurant')->where('item_id', $restaurant->id)->exists(),
            409,
            'Ce restaurant est utilise dans un package.'
        );

        $restaurant->delete();

        return response()->noContent();
    }
}
