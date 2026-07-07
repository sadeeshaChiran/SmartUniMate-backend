<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check 'locale' parameter (query string or body payload)
        // 2. Check 'locale' cookie (decrypted by Laravel automatically)
        // 3. Check 'Accept-Language' header
        $locale = $request->input('locale') ?? $request->cookie('locale') ?? $request->header('Accept-Language');

        if ($locale) {
            // Handle lists of locales (e.g., 'en-US,en;q=0.9,ta;q=0.8')
            $parts = explode(',', $locale);
            $primary = trim(explode(';', $parts[0])[0]);
            
            // Standardize: extract first 2 characters (e.g., 'en-US' -> 'en')
            $cleanLocale = strtolower(substr($primary, 0, 2));
            
            $supportedLocales = ['en', 'ta', 'si'];

            if (in_array($cleanLocale, $supportedLocales)) {
                app()->setLocale($cleanLocale);
            }
        }

        return $next($request);
    }
}
