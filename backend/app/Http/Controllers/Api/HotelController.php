<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HotelController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $hotels = Hotel::query()
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('stars'), fn ($query) => $query->where('stars', $request->integer('stars')))
            ->when($request->filled('price_min'), fn ($query) => $query->where('price_max', '>=', $request->input('price_min')))
            ->when($request->filled('price_max'), fn ($query) => $query->where('price_min', '<=', $request->input('price_max')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('description_fr', 'like', $search)
                        ->orWhere('description_en', 'like', $search)
                        ->orWhere('city', 'like', $search)
                        ->orWhere('district', 'like', $search);
                });
            })
            ->orderBy('city')
            ->orderBy('name')
            ->paginate($this->perPage($request));

        return HotelResource::collection($hotels);
    }

    public function show(int $id): HotelResource
    {
        return new HotelResource(Hotel::findOrFail($id));
    }
}
