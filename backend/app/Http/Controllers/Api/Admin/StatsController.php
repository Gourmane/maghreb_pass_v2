<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use App\Models\Favorite;
use App\Models\FootballMatch;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'matches' => FootballMatch::count(),
            'hotels' => Hotel::count(),
            'restaurants' => Restaurant::count(),
            'attractions' => Attraction::count(),
            'favorites' => Favorite::count(),
            'users' => User::count(),
            'tourists' => User::where('role', 'tourist')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ]);
    }
}
