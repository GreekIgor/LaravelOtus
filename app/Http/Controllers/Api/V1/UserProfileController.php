<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserProfileController extends Controller
{
    /**
     * GET /api/v1/me
     * Получить данные текущего пользователя
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        $cacheKey = 'api.me.' . md5(json_encode([
            'host' => $request->getSchemeAndHttpHost(),
            'user_id' => $user?->id,
        ]));

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return response()->json($cached);
        }

        $user->loadCount('recipes');
        $user->load(['recipes' => function ($query) {
            $query->select('recipes.id', 'recipes.title', 'recipes.created_at', 'recipes.user_id')
                ->orderByDesc('created_at')
                ->limit(5);
        }]);

        $payload = (new UserProfileResource($user))->response()->getData(true);
        Cache::put($cacheKey, $payload, now()->addSeconds(45));

        return response()->json($payload);
    }
}
