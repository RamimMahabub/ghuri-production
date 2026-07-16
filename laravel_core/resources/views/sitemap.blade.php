{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($staticUrls as $item)
    <url>
        <loc>{{ $item['url'] }}</loc>
        <changefreq>{{ $item['changefreq'] }}</changefreq>
        <priority>{{ $item['priority'] }}</priority>
    </url>
@endforeach
@foreach($properties as $property)
    <url>
        <loc>{{ route('hotels.show', $property) }}</loc>
        <lastmod>{{ $property->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
