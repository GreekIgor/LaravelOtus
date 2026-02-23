<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Переключение локали приложения
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Проверяем, что локаль поддерживается
        $supportedLocales = array_keys(config('locales.supported', []));
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('locales.default', 'ru');
        }

        // Устанавливаем локаль
        App::setLocale($locale);

        // Сохраняем в сессии
        if (config('locales.store_in_session', true)) {
            Session::put(config('locales.session_key', 'app_locale'), $locale);
        }

        // Редиректим обратно на предыдущую страницу или на главную
        $previousUrl = $request->header('referer');
        
        if ($previousUrl) {
            $parsedUrl = parse_url($previousUrl);
            $path = $parsedUrl['path'] ?? '/';
            $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
            
            // Удаляем старую локаль из пути (если была)
            $pathParts = explode('/', trim($path, '/'));
            if (!empty($pathParts) && in_array($pathParts[0], $supportedLocales)) {
                array_shift($pathParts);
            }
            
            // Если путь пустой, редиректим на главную
            if (empty($pathParts) || (count($pathParts) === 1 && empty($pathParts[0]))) {
                return redirect('/' . $query);
            }
            
            // Возвращаемся на ту же страницу без локали в URL
            $newPath = '/' . implode('/', $pathParts);
            $newPath = rtrim($newPath, '/');
            
            return redirect($newPath . $query);
        }

        // Если нет предыдущей страницы, редиректим на главную
        return redirect()->route('home');
    }
}

