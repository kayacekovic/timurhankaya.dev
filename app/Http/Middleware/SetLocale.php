<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryLang = $request->query('lang');

        if (is_string($queryLang) && in_array($queryLang, ['tr', 'en'], true) && $request->hasSession()) {
            $request->session()->put('locale', $queryLang);
        }

        $locale = $request->hasSession() ? $request->session()->get('locale') : null;

        if (! is_string($locale) || ! in_array($locale, ['tr', 'en'], true)) {
            $accept = $request->getPreferredLanguage(['tr', 'en']);
            $locale = is_string($accept) && $accept !== '' ? $accept : 'en';

            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
