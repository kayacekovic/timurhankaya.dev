<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->useRequestHostForViteInDev();
    }

    /**
     * When running Vite HMR, rewrite asset URLs to use the request host so that
     * devices on the same network (e.g. iPhone) can load CSS/JS from this machine.
     */
    protected function useRequestHostForViteInDev(): void
    {
        if (! app()->runningInConsole() && request()->getHost()) {
            $rewrite = function (string $url): array {
                $parsed = parse_url($url);
                if (! isset($parsed['host']) || $parsed['host'] === request()->getHost()) {
                    return [];
                }
                $scheme = request()->getScheme();
                $host = request()->getHost();
                $port = $parsed['port'] ?? ($parsed['scheme'] === 'https' ? 443 : 80);
                $path = $parsed['path'] ?? '';
                $query = isset($parsed['query']) ? '?'.$parsed['query'] : '';
                $newUrl = "{$scheme}://{$host}:{$port}{$path}{$query}";

                return [str_contains($url, '.css') ? 'href' : 'src' => $newUrl];
            };

            Vite::useScriptTagAttributes(fn (string $src, string $url, $chunk, $manifest) => $rewrite($url));
            Vite::useStyleTagAttributes(fn (string $src, string $url, $chunk, $manifest) => $rewrite($url));
        }
    }
}
