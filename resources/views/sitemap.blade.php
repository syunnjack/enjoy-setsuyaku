<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>{{ url('/') }}</loc><priority>1.0</priority></url>
  <url><loc>{{ route('articles.index') }}</loc><priority>0.8</priority></url>
  <url><loc>{{ route('cities.index') }}</loc><priority>0.8</priority></url>
  <url><loc>{{ route('about') }}</loc><priority>0.3</priority></url>
  <url><loc>{{ route('privacy') }}</loc><priority>0.3</priority></url>
  <url><loc>{{ route('terms') }}</loc><priority>0.3</priority></url>
  <url><loc>{{ route('contact') }}</loc><priority>0.3</priority></url>
@foreach ($articles as $article)
  <url><loc>{{ route('articles.show', $article['slug']) }}</loc><lastmod>{{ $article['updated_on'] ?? $article['published_on'] }}</lastmod><priority>0.9</priority></url>
@endforeach
@foreach ($cities as $city)
  <url><loc>{{ route('cities.show', $city['slug']) }}</loc><priority>0.7</priority></url>
@endforeach
@foreach ($items as $item)
  <url><loc>{{ route('items.show', $item['key']) }}</loc><priority>0.6</priority></url>
@endforeach
</urlset>
