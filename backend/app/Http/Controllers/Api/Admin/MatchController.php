<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpsertMatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\FootballMatch;
use App\Models\PackageItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MatchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return MatchResource::collection(
            FootballMatch::query()
                ->orderBy('match_date')
                ->orderBy('match_time')
                ->paginate($request->integer('per_page', 15))
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
        abort_if(
            PackageItem::where('item_type', 'match')->where('item_id', $match->id)->exists(),
            409,
            'Ce match est utilise dans un package.'
        );

        $match->delete();

        return response()->noContent();
    }
}
