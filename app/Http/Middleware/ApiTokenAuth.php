<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        // Пропускаем OPTIONS запросы (CORS preflight) без проверки токена
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        try {
            $token = $this->resolveToken($request);

            if (! $token) {
                return $this->unauthorized('API token is missing.');
            }

            $tokenHash = hash('sha256', $token);
            
            // Кэшируем токен на 5 минут для ускорения последующих запросов
            $cacheKey = "api_token:{$tokenHash}";
            $user = Cache::remember($cacheKey, 300, function () use ($tokenHash) {
                $apiToken = ApiToken::with('user:id,name,email,role')
                    ->where('token', $tokenHash)
                    ->first();
                
                return $apiToken && $apiToken->user ? $apiToken->user : null;
            });

            if (! $user) {
                // Если токен не найден, очищаем кэш на случай, если токен был удален
                Cache::forget($cacheKey);
                return $this->unauthorized('Invalid API token.');
            }

            Auth::setUser($user);

            return $next($request);
        } catch (\Throwable $e) {
            // Логируем только ошибки
            Log::error('API Token Auth Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->unauthorized('Authentication error occurred.');
        }
    }

    protected function resolveToken(Request $request): ?string
    {
        // Проверяем заголовок Authorization
        $header = $request->header('Authorization');

        if ($header) {
            // Поддерживаем разные форматы: "Bearer token" и "bearer token"
            if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
                return trim($matches[1]);
            }
        }

        // Проверяем query параметр
        return $request->query('api_token') ?: null;
    }

    protected function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }
}

