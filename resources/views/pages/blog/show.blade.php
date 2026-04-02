@php
    $publishedAt = $post->published_at ?? $post->created_at;
    $modifiedAt = $post->updated_at ?? $publishedAt;
    $postUrl = \App\Support\Seo::route('blog.show', ['post' => $post->slug]);
    $blogUrl = \App\Support\Seo::route('blog.index');
    $postDescription = $post->excerpt ?: \App\Support\Seo::descriptionFromHtml($post->content);
    $seo = [
        'title' => $post->title,
        'description' => $postDescription,
        'canonical' => $postUrl,
        'image' => $post->cover_image ?: 'og-blog-post.png',
        'type' => 'article',
        'author' => 'Timurhan Kaya',
        'section' => 'Blog',
        'published_time' => $publishedAt,
        'modified_time' => $modifiedAt,
    ];
@endphp

@extends('layouts.app')

@section('title', $post->title)

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'BlogPosting',
                    'headline' => $post->title,
                    'description' => $postDescription,
                    'url' => $postUrl,
                    'datePublished' => \App\Support\Seo::normalizeDate($publishedAt),
                    'dateModified' => \App\Support\Seo::normalizeDate($modifiedAt),
                    'author' => [
                        '@type' => 'Person',
                        'name' => 'Timurhan Kaya',
                    ],
                    'publisher' => [
                        '@type' => 'Person',
                        'name' => 'Timurhan Kaya',
                    ],
                    'image' => [\App\Support\Seo::imageUrl($post->cover_image ?: 'og-blog-post.png')],
                    'mainEntityOfPage' => $postUrl,
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
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $post->title,
                            'item' => $postUrl,
                        ],
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <style>
        .blog-content {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #3f3f46;
            line-height: 1.75;
            font-size: 1.125rem;
        }
        .dark .blog-content {
            color: #d4d4d8;
        }

        .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4, .blog-content h5, .blog-content h6 {
            color: #09090b;
            font-weight: 700;
            margin-top: 2em;
            margin-bottom: 1em;
            line-height: 1.3;
        }
        .dark .blog-content a { color: #818cf8; }
        .blog-content a { color: #4f46e5; text-decoration: underline; font-weight: 500; }

        .dark .blog-content h1, .dark .blog-content h2, .dark .blog-content h3, .dark .blog-content h4, .dark .blog-content h5, .dark .blog-content h6 {
            color: #ffffff;
        }

        .blog-content h1 { font-size: 2.25em; margin-top: 0; }
        .blog-content h2 { font-size: 1.5em; }
        .blog-content h3 { font-size: 1.25em; }
        .blog-content h4 { font-size: 1.125em; }

        .blog-content p { margin-top: 1.25em; margin-bottom: 1.25em; }

        .blog-content blockquote {
            font-weight: 500;
            font-style: italic;
            color: #18181b;
            border-left: 0.25rem solid #e4e4e7;
            border-radius: 0.25rem;
            quotes: "\201C""\201D""\2018""\2019";
            margin-top: 1.6em;
            margin-bottom: 1.6em;
            padding-left: 1em;
        }
        .dark .blog-content blockquote {
            color: #f4f4f5;
            border-left-color: #3f3f46;
        }

        .blog-content ul { list-style-type: disc; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
        .blog-content ol { list-style-type: decimal; margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
        .blog-content li { margin-top: 0.5em; margin-bottom: 0.5em; }

        .blog-content ul > li::marker { color: #d4d4d8; }
        .dark .blog-content ul > li::marker { color: #52525b; }

        .blog-content hr {
            border-color: #e4e4e7;
            margin-top: 3em;
            margin-bottom: 3em;
        }
        .dark .blog-content hr { border-color: #27272a; }

        .blog-content img {
            border-radius: 0.75rem;
            margin-top: 2em;
            margin-bottom: 2em;
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .blog-content pre {
            background-color: #18181b;
            color: #e4e4e7;
            overflow-x: auto;
            border-radius: 0.75rem;
            padding: 1.25em 1.5em;
            margin-top: 1.7em;
            margin-bottom: 1.7em;
            font-size: 0.875em;
            line-height: 1.7142857;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        .dark .blog-content pre {
            background-color: #09090b;
            border: 1px solid #27272a;
        }

        .blog-content code {
            color: #18181b;
            font-weight: 600;
            font-size: 0.875em;
            background: #f4f4f5;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
        }
        .dark .blog-content code { color: #e4e4e7; background: #27272a; }

        .blog-content pre code {
            background: transparent;
            color: inherit;
            font-weight: inherit;
            font-size: inherit;
            padding: 0;
            border-radius: 0;
        }

        .blog-content table {
            width: 100%;
            table-layout: auto;
            text-align: left;
            margin-top: 2em;
            margin-bottom: 2em;
            font-size: 0.875em;
            line-height: 1.5;
            border-collapse: collapse;
        }

        .blog-content thead {
            border-bottom: 1px solid #d4d4d8;
        }
        .dark .blog-content thead { border-bottom-color: #3f3f46; }

        .blog-content thead th {
            color: #09090b;
            font-weight: 600;
            padding-bottom: 0.5714286em;
            padding-right: 0.5714286em;
            vertical-align: bottom;
        }
        .dark .blog-content thead th { color: #ffffff; }

        .blog-content tbody tr {
            border-bottom: 1px solid #e4e4e7;
        }
        .dark .blog-content tbody tr { border-bottom-color: #27272a; }

        .blog-content tbody td {
            padding-top: 0.5714286em;
            padding-right: 0.5714286em;
            padding-bottom: 0.5714286em;
            vertical-align: top;
        }
    </style>
@endpush

@section('content')
<div class="pt-[calc(4rem+env(safe-area-inset-top)+2rem)] pb-24 sm:pt-[calc(5rem+env(safe-area-inset-top)+2rem)]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ $blogUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-100 transition mb-6">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('blog.backToBlog') }}
        </a>

        <article class="relative rounded-3xl border border-zinc-200/50 bg-white/40 p-6 md:p-12 backdrop-blur dark:border-white/5 dark:bg-zinc-950/40 shadow-sm">
            <header class="mb-10 text-center md:text-left border-b border-zinc-200 dark:border-white/10 pb-8 mt-6">
                <div class="flex items-center gap-x-4 text-xs justify-center md:justify-start">
                    <time datetime="{{ optional($publishedAt)?->format('Y-m-d') }}" class="text-zinc-500 dark:text-zinc-400 font-mono">
                        {{ optional($publishedAt)?->format('M j, Y') }}
                    </time>
                </div>
                <h1 class="mt-6 text-3xl sm:text-5xl font-extrabold tracking-tight text-zinc-950 dark:text-white leading-tight">
                    {{ $post->title }}
                </h1>
                @if($postDescription)
                    <p class="mt-6 text-lg md:text-xl text-zinc-600 dark:text-zinc-300 max-w-2xl leading-relaxed">
                        {{ $postDescription }}
                    </p>
                @endif
            </header>

            @if($post->cover_image)
                <div class="mb-10 w-full overflow-hidden rounded-2xl ring-1 ring-zinc-200 dark:ring-white/10 shadow-lg">
                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-auto object-cover max-h-[500px]">
                </div>
            @endif

            <div class="blog-content">
                {!! $post->content !!}
            </div>
        </article>

    </div>
</div>
@endsection
