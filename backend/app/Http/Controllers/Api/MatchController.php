<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\FootballMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatchController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $matches = FootballMatch::query()
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->input('city')))
            ->when($request->filled('group_name'), fn ($query) => $query->where('group_name', $request->input('group_name')))
            ->when($request->filled('phase'), fn ($query) => $query->where('phase', $request->input('phase')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('match_date', $request->input('date')))
            ->when($request->filled('match_date'), fn ($query) => $query->whereDate('match_date', $request->input('match_date')))
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->paginate($request->integer('per_page', 12));

        return MatchResource::collection($matches);
    }

    public function show(int $id): MatchResource
    {
        return new MatchResource(FootballMatch::findOrFail($id));
    }
}
