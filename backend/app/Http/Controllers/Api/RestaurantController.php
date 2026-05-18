<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RestaurantController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $restaurants = Restaurant::query()
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('cuisine_type'), fn ($query) => $query->where('cuisine_type', $request->input('cuisine_type')))
            ->when($request->filled('price_range'), fn ($query) => $query->where('price_range', $request->input('price_range')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('description_fr', 'like', $search)
                        ->orWhere('description_en', 'like', $search)
                        ->orWhere('city', 'like', $search)
                        ->orWhere('address', 'like', $search)
                        ->orWhere('cuisine_type', 'like', $search);
                });
            })
            ->orderBy('city')
            ->orderBy('name')
            ->paginate($request->integer('per_page', 12));

        return RestaurantResource::collection($restaurants);
    }

    public function show(int $id): RestaurantResource
    {
        return new RestaurantResource(Restaurant::findOrFail($id));
    }
}
