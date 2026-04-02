{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($items as $item)
    <url>
        <loc>{{ $item['loc'] }}</loc>
        @foreach ($item['alternates'] ?? [] as $hreflang => $href)
            <xhtml:link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}" />
        @endforeach
        @if (! empty($item['lastmod']))
            <lastmod>{{ $item['lastmod'] }}</lastmod>
        @endif
        @if (! empty($item['changefreq']))
            <changefreq>{{ $item['changefreq'] }}</changefreq>
        @endif
        @if (! empty($item['priority']))
            <priority>{{ $item['priority'] }}</priority>
        @endif
    </url>
@endforeach
</urlset>
