<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set locale from Accept-Language header or query param.
 * Default locale is 'vn'.
 */
class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'vn'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function determineLocale(Request $request): string
    {
        // 1. Check query parameter (?lang=en)
        if ($request->query('lang') && in_array($request->query('lang'), self::SUPPORTED_LOCALES, true)) {
            return $request->query('lang');
        }

        // 2. Check header (Accept-Language)
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = $this->parseAcceptLanguage($acceptLanguage);
            if ($locale !== null) {
                return $locale;
            }
        }

        // 3. Check user preference (if authenticated)
        if (auth()->check() && auth()->user()?->locale) {
            $userLocale = auth()->user()->locale;
            if (in_array($userLocale, self::SUPPORTED_LOCALES, true)) {
                return $userLocale;
            }
        }

        // 4. Default to Vietnamese
        return 'vn';
    }

    private function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $languages = explode(',', $acceptLanguage);

        foreach ($languages as $language) {
            $locale = trim(explode(';', $language)[0]);
            $locale = explode('-', $locale)[0]; // e.g., "en-US" -> "en"

            if (in_array($locale, self::SUPPORTED_LOCALES, true)) {
                return $locale;
            }
        }

        return null;
    }
}
