<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Admin\Concerns\ProtectsCatalogItemDeletion;
use App\Http\Requests\Api\UpsertMatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\FootballMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MatchController extends Controller
{
    use ProtectsCatalogItemDeletion;

    public function index(Request $request): AnonymousResourceCollection
    {
        return MatchResource::collection(
            FootballMatch::query()
                ->orderBy('match_date')
                ->orderBy('match_time')
                ->paginate($this->perPage($request, 15))
        );
    }

    public function store(UpsertMatchRequest $request): MatchResource
    {
        return new MatchResource(FootballMatch::create($request->validated()));
    }

    public function show(FootballMatch $match): MatchResource
    {
        return new MatchResource($match);
    }

    public function update(UpsertMatchRequest $request, FootballMatch $match): MatchResource
    {
        $match->update($request->validated());

        return new MatchResource($match);
    }

    public function destroy(FootballMatch $match): Response
    {
        $this->abortIfCatalogItemIsUsed('match', $match->id);

        $match->delete();

        return response()->noContent();
    }
}
