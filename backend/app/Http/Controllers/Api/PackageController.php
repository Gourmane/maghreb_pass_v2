<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $packages = TravelPackage::query()
            ->withCount('items')
            ->where('is_active', true)
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->input('search').'%';

                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', $search)
                        ->orWhere('description_fr', 'like', $search)
                        ->orWhere('description_en', 'like', $search)
                        ->orWhere('city', 'like', $search);
                });
            })
            ->orderBy('city')
            ->orderBy('title')
            ->paginate($request->integer('per_page', 12));

        return PackageResource::collection($packages);
    }

    public function show(int $id): PackageResource
    {
        $package = TravelPackage::query()
            ->with('items')
            ->withCount('items')
            ->where('is_active', true)
            ->findOrFail($id);

        return new PackageResource($package);
    }
}
