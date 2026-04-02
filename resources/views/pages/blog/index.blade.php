@php
    $blogUrl = \App\Support\Seo::route('blog.index');
    $canonicalUrl = request()->fullUrlWithoutQuery('lang');
    $seo = [
        'title' => __('blog.metaTitle'),
        'description' => __('blog.metaDescription'),
        'canonical' => $canonicalUrl,
        'image' => 'og-blog.svg',
    ];
@endphp

@extends('layouts.app')

@section('title', __('blog.metaTitle'))

@push('head')
    @php
        $itemOffset = $posts->firstItem() ?? 1;
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => __('blog.metaTitle'),
                    'url' => \App\Support\Seo::localizedUrl($canonicalUrl),
                    'description' => __('blog.metaDescription'),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => \App\Support\Seo::siteName(),
                            'item' => \App\Support\Seo::route('home'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => __('blog.title'),
                            'item' => $blogUrl,
                        ],
                    ],
                ],
                [
                    '@type' => 'ItemList',
                    'itemListElement' => $posts->getCollection()->values()->map(
                        fn ($post, int $index): array => [
                            '@type' => 'ListItem',
                            'position' => $itemOffset + $index,
                            'url' => \App\Support\Seo::route('blog.show', ['post' => $post->slug]),
                            'name' => $post->title,
                        ],
                    )->all(),
                ],
            ],
        ];
    @endphp
    @if ($posts->previousPageUrl())
        <link rel="prev" href="{{ \App\Support\Seo::localizedUrl($posts->previousPageUrl()) }}">
    @endif
    @if ($posts->nextPageUrl())
        <link rel="next" href="{{ \App\Support\Seo::localizedUrl($posts->nextPageUrl()) }}">
    @endif
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<div class="pt-[calc(4rem+env(safe-area-inset-top)+2rem)] sm:pt-[calc(5rem+env(safe-area-inset-top)+2rem)]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-12 text-center md:text-left">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-zinc-950 dark:text-white">
                <span class="cyber-gradient-text">{{ __('blog.title') }}</span>
            </h1>
            <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl">
                {{ __('blog.lead') }}
            </p>
        </header>

        <div class="space-y-10">
            @forelse($posts as $post)
                <article class="group relative flex flex-col items-start justify-between rounded-3xl border border-zinc-200/50 bg-white/40 p-6 md:p-8 backdrop-blur transition-all hover:bg-white/80 dark:border-white/5 dark:bg-zinc-950/40 dark:hover:bg-zinc-950/60 shadow-xs hover:shadow-md dark:shadow-black/20">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="{{ optional($post->published_at ?? $post->created_at)?->format('Y-m-d') }}" class="text-zinc-500 dark:text-zinc-400 font-mono">
                            {{ optional($post->published_at ?? $post->created_at)?->format('M j, Y') }}
                        </time>
                        <a href="{{ \App\Support\Seo::route('blog.show', ['post' => $post->slug]) }}" class="relative z-10 rounded-full bg-zinc-100 px-3 py-1.5 font-semibold text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">{{ __('blog.readArticle') }}</a>
                    </div>
                    <div class="group relative mt-4">
                        <h2 class="mt-3 text-2xl font-semibold leading-6 text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            <a href="{{ \App\Support\Seo::route('blog.show', ['post' => $post->slug]) }}">
                                <span class="absolute inset-0"></span>
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="mt-4 line-clamp-3 text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ $post->excerpt ?: \App\Support\Seo::descriptionFromHtml($post->content, 150) }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="text-center py-20 rounded-3xl border border-dashed border-zinc-200 dark:border-white/10">
                    <p class="text-zinc-500 dark:text-zinc-400">{{ __('blog.empty') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
