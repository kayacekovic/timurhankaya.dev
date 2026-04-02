<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('renders localized canonical and alternate links on the home page', function () {
    $response = $this->get('/?lang=tr');

    $response->assertOk();
    $content = $response->getContent();

    preg_match('/<link rel="canonical" href="([^"]+)">/', $content, $canonical);
    preg_match('/<link rel="alternate" hreflang="en" href="([^"]+)">/', $content, $alternateEn);
    preg_match('/<link rel="alternate" hreflang="tr" href="([^"]+)">/', $content, $alternateTr);

    expect($canonical[1] ?? null)->toEndWith('?lang=tr');
    expect($alternateEn[1] ?? null)->toEndWith('?lang=en');
    expect($alternateTr[1] ?? null)->toEndWith('?lang=tr');
});

it('serves robots txt with an absolute sitemap url', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    expect($response->getContent())->toContain('/sitemap.xml');
});

it('only exposes published posts in blog routes and sitemap', function () {
    $published = Post::create([
        'title' => 'Published Post',
        'slug' => 'published-post',
        'excerpt' => 'Published excerpt',
        'content' => '<p>Published body</p>',
        'published_at' => Carbon::parse('2026-03-20 12:00:00'),
    ]);

    Post::create([
        'title' => 'Draft Post',
        'slug' => 'draft-post',
        'excerpt' => 'Draft excerpt',
        'content' => '<p>Draft body</p>',
        'published_at' => null,
    ]);

    $this->get('/blog')
        ->assertOk()
        ->assertSee('Published Post')
        ->assertDontSee('Draft Post');

    $this->get('/blog/published-post')
        ->assertOk()
        ->assertSee('Published Post');

    $this->get('/blog/draft-post')
        ->assertNotFound();

    $sitemap = $this->get('/sitemap.xml');

    $sitemap->assertOk();
    $content = $sitemap->getContent();

    expect(str_contains($content, 'blog/published-post?lang=en'))->toBeTrue();
    expect(str_contains($content, 'blog/published-post?lang=tr'))->toBeTrue();
    expect(str_contains($content, 'draft-post'))->toBeFalse();
});

it('renders article seo metadata on blog detail pages', function () {
    $post = Post::create([
        'title' => 'SEO Article',
        'slug' => 'seo-article',
        'cover_image' => '/images/seo-cover.png',
        'excerpt' => 'A focused article excerpt.',
        'content' => '<p>Article body</p>',
        'published_at' => Carbon::parse('2026-03-18 09:30:00'),
    ]);
    $post->forceFill([
        'updated_at' => Carbon::parse('2026-03-19 10:45:00'),
    ])->save();

    $response = $this->get('/blog/seo-article?lang=en');

    $response->assertOk();
    $content = $response->getContent();

    expect(str_contains($content, 'property="og:type" content="article"'))->toBeTrue();
    preg_match('/<link rel="canonical" href="([^"]+)">/', $content, $canonical);
    expect($canonical[1] ?? null)->toEndWith('/blog/seo-article?lang=en');
    expect(str_contains($content, 'property="article:published_time" content="2026-03-18T09:30:00+00:00"'))->toBeTrue();
    expect(str_contains($content, 'property="article:modified_time" content="2026-03-19T10:45:00+00:00"'))->toBeTrue();
    expect(str_contains($content, 'application/ld+json'))->toBeTrue();
});
