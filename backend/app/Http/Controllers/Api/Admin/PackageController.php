<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertPackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PackageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PackageResource::collection(
            TravelPackage::query()
                ->withCount('items')
                ->orderBy('city')
                ->orderBy('title_fr')
                ->paginate($this->perPage($request, 15))
        );
    }

    public function store(UpsertPackageRequest $request): PackageResource
    {
        $data = $request->validated();
        $data['currency'] ??= 'MAD';
        $data['is_active'] ??= true;

        return new PackageResource(TravelPackage::create($data));
    }

    public function show(TravelPackage $package): PackageResource
    {
        return new PackageResource($package->load('items')->loadCount('items'));
    }

    public function update(UpsertPackageRequest $request, TravelPackage $package): PackageResource
    {
        $data = $request->validated();
        $data['currency'] ??= 'MAD';

        $package->update($data);

        return new PackageResource($package->load('items')->loadCount('items'));
    }

    public function destroy(TravelPackage $package): Response
    {
        $package->delete();

        return response()->noContent();
    }
}
