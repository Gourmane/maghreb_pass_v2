<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'preferred_language', 'is_active', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage($request, 15));

        return response()->json($users);
    }

    public function toggle(Request $request, User $user): JsonResponse
    {
        if ($user->is_active && $request->user()?->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'Vous ne pouvez pas desactiver votre propre compte administrateur.',
            ]);
        }

        if ($user->is_active && $user->role === 'admin' && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'Vous ne pouvez pas desactiver le dernier administrateur actif.',
            ]);
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return response()->json([
            'message' => $user->is_active ? 'Utilisateur reactive.' : 'Utilisateur desactive.',
            'user' => $user->only(['id', 'name', 'email', 'role', 'preferred_language', 'is_active']),
        ]);
    }
}
