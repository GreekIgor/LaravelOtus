<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * GET /api/v1/me
     * Получить данные текущего пользователя
     */
    public function show(Request $request): UserProfileResource
    {
        $user = Auth::user();
        
        // Загружаем количество рецептов и последние рецепты
        $user->loadCount('recipes');
        $user->load(['recipes' => function ($query) {
            $query->orderByDesc('created_at')->limit(5);
        }]);

        return new UserProfileResource($user);
    }
}
