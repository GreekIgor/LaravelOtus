<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->resolveToken($request);

        if (! $token) {
            return $this->unauthorized('API token is missing.');
        }

        /** @var \App\Models\ApiToken|null $apiToken */
        $apiToken = ApiToken::with('user')
            ->where('token', hash('sha256', $token))
            ->first();

        if (! $apiToken || ! $apiToken->user) {
            return $this->unauthorized('Invalid API token.');
        }

        // Обновляем время последнего использования и авторизуем пользователя для текущего запроса
        $apiToken->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($apiToken->user);

        return $next($request);
    }

    protected function resolveToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return $request->query('api_token') ?: null;
    }

    protected function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}

