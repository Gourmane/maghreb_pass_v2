<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttractionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $attractions = Attraction::query()
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('description_fr', 'like', $search)
                        ->orWhere('description_en', 'like', $search)
                        ->orWhere('city', 'like', $search)
                        ->orWhere('address', 'like', $search)
                        ->orWhere('category', 'like', $search);
                });
            })
            ->orderBy('city')
            ->orderBy('name')
            ->paginate($this->perPage($request));

        return AttractionResource::collection($attractions);
    }

    public function show(int $id): AttractionResource
    {
        return new AttractionResource(Attraction::findOrFail($id));
    }
}
