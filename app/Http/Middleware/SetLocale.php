<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для установки локали на основе сессии, cookie или заголовков браузера
 * Соответствует PSR-7 и PSR-15 через Laravel's Request/Response
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);
        
        // Устанавливаем локаль в приложении
        App::setLocale($locale);
        
        // Сохраняем локаль в сессии для последующих запросов
        if (config('locales.store_in_session', true)) {
            Session::put(config('locales.session_key', 'app_locale'), $locale);
        }
        
        return $next($request);
    }

    /**
     * Определение локали из запроса
     */
    protected function detectLocale(Request $request): string
    {
        $supportedLocales = array_keys(config('locales.supported', []));
        $defaultLocale = config('locales.default', 'ru');
        
        // Метод 1: Из сессии (приоритет)
        if (config('locales.store_in_session', true)) {
            $localeFromSession = Session::get(config('locales.session_key', 'app_locale'));
            if ($localeFromSession && in_array($localeFromSession, $supportedLocales)) {
                return $localeFromSession;
            }
        }
        
        // Метод 2: Из cookie (если есть)
        $localeFromCookie = $request->cookie('app_locale');
        if ($localeFromCookie && in_array($localeFromCookie, $supportedLocales)) {
            return $localeFromCookie;
        }
        
        // Метод 3: Из заголовков браузера (Accept-Language)
        $localeFromBrowser = $request->getPreferredLanguage($supportedLocales);
        if ($localeFromBrowser) {
            return $localeFromBrowser;
        }
        
        // Метод 4: Дефолтная локаль
        return $defaultLocale;
    }
}

