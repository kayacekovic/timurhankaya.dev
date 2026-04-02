<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class Seo
{
    /**
     * @var list<string>
     */
    private const SUPPORTED_LOCALES = ['en', 'tr'];

    /**
     * @return list<string>
     */
    public static function supportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    public static function fallbackLocale(): string
    {
        $locale = (string) config('app.fallback_locale', 'en');

        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : 'en';
    }

    public static function currentLocale(): string
    {
        $locale = app()->getLocale();

        return in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : self::fallbackLocale();
    }

    public static function siteName(): string
    {
        $configured = trim((string) config('app.name', ''));

        if ($configured !== '' && $configured !== 'Laravel') {
            return $configured;
        }

        return 'Timurhan Kaya';
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        $parameters['lang'] = $locale ?? self::currentLocale();

        return route($name, $parameters, $absolute);
    }

    public static function localizedUrl(?string $url = null, ?string $locale = null): string
    {
        $resolvedUrl = $url ?: url()->current();
        $resolvedLocale = in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : self::currentLocale();

        $parts = parse_url($resolvedUrl);

        if ($parts === false) {
            return $resolvedUrl;
        }

        $query = [];

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['lang'] = $resolvedLocale;
        ksort($query);

        $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':'.$parts['pass'] : '';
        $auth = $user !== '' ? $user.$pass.'@' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';
        $queryString = http_build_query($query);
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return "{$scheme}{$auth}{$host}{$port}{$path}".($queryString !== '' ? "?{$queryString}" : '').$fragment;
    }

    /**
     * @return array<string, string>
     */
    public static function alternateUrls(?string $url = null): array
    {
        $resolvedUrl = $url ?: url()->current();
        $alternates = [];

        foreach (self::SUPPORTED_LOCALES as $locale) {
            $alternates[$locale] = self::localizedUrl($resolvedUrl, $locale);
        }

        $alternates['x-default'] = self::localizedUrl($resolvedUrl, self::fallbackLocale());

        return $alternates;
    }

    public static function ogLocale(?string $locale = null): string
    {
        return ($locale ?? self::currentLocale()) === 'tr' ? 'tr_TR' : 'en_US';
    }

    public static function imageUrl(?string $image = null): string
    {
        if (is_string($image) && $image !== '' && Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        return asset(ltrim((string) ($image ?: 'og.svg'), '/'));
    }

    public static function normalizeDate(DateTimeInterface|string|null $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof DateTimeInterface) {
                return Carbon::instance($value)->toAtomString();
            }

            return Carbon::parse($value)->toAtomString();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  list<string>  $paths
     */
    public static function lastModifiedForPaths(array $paths): ?string
    {
        $timestamps = collect($paths)
            ->filter(fn (mixed $path): bool => is_string($path) && is_file($path))
            ->map(fn (string $path): int => (int) filemtime($path))
            ->filter(fn (int $timestamp): bool => $timestamp > 0)
            ->values();

        if ($timestamps->isEmpty()) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamps->max())->toAtomString();
    }

    public static function descriptionFromHtml(?string $html, int $limit = 160): string
    {
        $plain = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $html)));

        return Str::limit($plain, $limit);
    }
}
