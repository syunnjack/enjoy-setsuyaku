@extends('layouts.app')

@section('title', $article['title'].' | '.config('app.name'))
@section('description', $article['description'])
@section('og_type', 'article')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => url('/')],
      ['@type' => 'ListItem', 'position' => 2, 'name' => '記事一覧', 'item' => route('articles.index')],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title'], 'item' => route('articles.show', $article['slug'])],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'Article',
  'headline' => $article['title'],
  'description' => $article['description'],
  'datePublished' => $article['published_on'],
  'dateModified' => $article['updated_on'] ?? $article['published_on'],
  'author' => ['@type' => 'Organization', 'name' => config('app.name')],
  'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
  'mainEntityOfPage' => route('articles.show', $article['slug']),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container my-4" style="max-width: 760px;">
  <nav class="small mb-3">
    <a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span>
    <a href="{{ route('articles.index') }}">記事一覧</a> <span class="text-muted mx-1">/</span>
    <span class="text-muted">{{ $article['category'] }}</span>
  </nav>

  <article>
    <h1 class="h3 fw-bold">{{ $article['title'] }}</h1>
    <p class="small text-muted">
      {{ $article['published_on'] }} 公開
      @if(!empty($article['updated_on'])) ・{{ $article['updated_on'] }} 更新 @endif
      ・{{ $article['reading_minutes'] }}分で読めます
    </p>

    <div class="article-body">
      {!! $article['html'] !!}
    </div>
  </article>

  @if($related->isNotEmpty())
    <h2 class="h6 mt-5">同じカテゴリの記事</h2>
    <div class="row g-3">
      @foreach($related as $other)
        <div class="col-12 col-md-6">
          <a href="{{ route('articles.show', $other['slug']) }}" class="d-block p-3 h-100 text-decoration-none card-soft">
            <div class="fw-semibold small">{{ $other['title'] }}</div>
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <p class="small text-muted mt-4">
    この記事の金額は、記載した公的資料の公表値です。制度や料金は変わることがあるため、
    実際の手続きの前にリンク先の一次情報をご確認ください。
  </p>
</div>
@endsection
