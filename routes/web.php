<?php

use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Services\Portfolio\PortfolioContentService;
use App\Support\Seo;
use Illuminate\Support\Facades\Route;

Route::get('/', function (PortfolioContentService $portfolio) {
    return view('pages.home', [
        'content' => $portfolio->get(),
    ]);
})->name('home');

Route::get('/contact', function (PortfolioContentService $portfolio) {
    return view('pages.contact', [
        'content' => $portfolio->get(),
    ]);
})->name('contact');

Route::get('/cv', function () {
    return view('pages.cv');
})->name('cv');

Route::get('/robots.txt', function () {
    $content = implode("\n", [
        'User-agent: *',
        'Allow: /',
        '',
        'Sitemap: '.route('sitemap'),
    ])."\n";

    return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::get('/sitemap.xml', function () {
    $pages = [
        [
            'route' => 'home',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/home.blade.php'),
                resource_path('content/portfolio.en.json'),
                resource_path('content/portfolio.tr.json'),
            ]),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
        [
            'route' => 'contact',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/contact.blade.php'),
                resource_path('content/portfolio.en.json'),
                resource_path('content/portfolio.tr.json'),
            ]),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        [
            'route' => 'cv',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/cv.blade.php'),
            ]),
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        [
            'route' => 'games.index',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/games/index.blade.php'),
                resource_path('views/pages/games/imposter/index.blade.php'),
                resource_path('views/pages/games/vampire/index.blade.php'),
                resource_path('lang/en.json'),
                resource_path('lang/tr.json'),
            ]),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'route' => 'games.imposter.index',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/games/imposter/index.blade.php'),
                resource_path('views/livewire/games/imposter/index.blade.php'),
                resource_path('lang/en.json'),
                resource_path('lang/tr.json'),
            ]),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ],
        [
            'route' => 'games.vampire.index',
            'lastmod' => Seo::lastModifiedForPaths([
                resource_path('views/pages/games/vampire/index.blade.php'),
                resource_path('views/livewire/games/vampire/index.blade.php'),
                resource_path('lang/en.json'),
                resource_path('lang/tr.json'),
            ]),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ],
        [
            'route' => 'blog.index',
            'lastmod' => Seo::normalizeDate(
                Post::published()->max('updated_at') ?? Seo::lastModifiedForPaths([
                    resource_path('views/pages/blog/index.blade.php'),
                ]),
            ),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ],
    ];

    $items = collect($pages)
        ->flatMap(function (array $page): array {
            $url = route($page['route']);
            $alternates = Seo::alternateUrls($url);

            return collect(Seo::supportedLocales())
                ->map(fn (string $locale): array => [
                    'loc' => Seo::localizedUrl($url, $locale),
                    'lastmod' => $page['lastmod'],
                    'changefreq' => $page['changefreq'],
                    'priority' => $page['priority'],
                    'alternates' => $alternates,
                ])
                ->all();
        })
        ->values()
        ->all();

    $posts = Post::published()
        ->orderByDesc('published_at')
        ->get()
        ->flatMap(function (Post $post): array {
            $url = route('blog.show', $post);
            $alternates = Seo::alternateUrls($url);
            $lastmod = Seo::normalizeDate($post->updated_at ?? $post->published_at);

            return collect(Seo::supportedLocales())
                ->map(fn (string $locale): array => [
                    'loc' => Seo::localizedUrl($url, $locale),
                    'lastmod' => $lastmod,
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                    'alternates' => $alternates,
                ])
                ->all();
        })
        ->values()
        ->all();

    return response()
        ->view('sitemap.xml', ['items' => [...$items, ...$posts]], 200)
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::prefix('games')->name('games.')->group(function (): void {
    Route::view('/', 'pages.games.index')->name('index');

    Route::view('/imposter', 'pages.games.imposter.index')->name('imposter.index');
    Route::view('/imposter/{roomCode}', 'pages.games.imposter.room')
        ->where('roomCode', '[A-Z0-9]{4,10}')
        ->name('imposter.room');

    Route::view('/vampire', 'pages.games.vampire.index')->name('vampire.index');
    Route::view('/vampire/{roomCode}', 'pages.games.vampire.room')
        ->where('roomCode', '[A-Z0-9]{4,10}')
        ->name('vampire.room');
});

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [PostController::class, 'show'])->name('show');
});
