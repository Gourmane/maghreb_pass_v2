<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\HandlesPhotoUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertAttractionRequest;
use App\Http\Resources\AttractionResource;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AttractionController extends Controller
{
    use HandlesPhotoUploads;

    public function index(Request $request): AnonymousResourceCollection
    {
        return AttractionResource::collection(
            Attraction::query()
                ->orderBy('city')
                ->orderBy('name')
                ->paginate($request->integer('per_page', 15))
        );
    }

    public function store(UpsertAttractionRequest $request): AttractionResource
    {
        return new AttractionResource(Attraction::create(
            $this->validatedDataWithPhotoUploads($request, 'uploads/attractions')
        ));
    }

    public function show(Attraction $attraction): AttractionResource
    {
        return new AttractionResource($attraction);
    }

    public function update(UpsertAttractionRequest $request, Attraction $attraction): AttractionResource
    {
        $attraction->update($this->validatedDataWithPhotoUploads($request, 'uploads/attractions'));

        return new AttractionResource($attraction);
    }

    public function destroy(Attraction $attraction): Response
    {
        $attraction->delete();

        return response()->noContent();
    }
}
