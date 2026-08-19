@extends('layouts.app')

@section('title', '記事一覧（'.$total.'本） | '.config('app.name'))
@section('description', '光熱費・通信費・食費・保険・教育費など、家計の見直しに関する記事'.$total.'本を掲載しています。金額はすべて公的統計にもとづいています。')

@section('content')
<div class="container my-4" style="max-width: 860px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">記事一覧</span></nav>

  <h1 class="h4 fw-bold">記事一覧</h1>
  <p class="text-muted">{{ $total }}本</p>

  @foreach($categories as $category => $articles)
    <h2 class="h6 mt-4">{{ $category }}</h2>
    <div class="row g-3">
      @foreach($articles as $article)
        <div class="col-12 col-md-6">
          <a href="{{ route('articles.show', $article['slug']) }}" class="d-block p-3 h-100 text-decoration-none card-soft">
            <div class="fw-semibold">{{ $article['title'] }}</div>
            <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($article['description'], 80) }}</div>
          </a>
        </div>
      @endforeach
    </div>
  @endforeach
</div>
@endsection
