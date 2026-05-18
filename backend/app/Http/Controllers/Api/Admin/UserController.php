<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'preferred_language', 'is_active', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json($users);
    }

    public function toggle(User $user): JsonResponse
    {
        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return response()->json([
            'message' => $user->is_active ? 'Utilisateur reactive.' : 'Utilisateur desactive.',
            'user' => $user->only(['id', 'name', 'email', 'role', 'preferred_language', 'is_active']),
        ]);
    }
}
