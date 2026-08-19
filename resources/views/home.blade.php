@extends('layouts.app')

@section('title', config('app.name').' | 家計調査で見る、くらしの支出と節約')
@section('description', '二人以上の世帯の消費支出は月平均'.number_format($national['total']).'円（総務省 家計調査 '.\App\Support\Kakei::surveyYear().'年）。費目別・都市別の平均を確かめてから、節約の順番を決められます。')

@section('content')
<div class="container my-4" style="max-width: 900px;">
  <div class="text-center my-4">
    <h1 class="h3 fw-bold">数字を確かめてから、節約する</h1>
    <p class="text-muted">
      「なんとなく高い気がする」で判断せずに、まず平均と比べます。<br class="d-none d-md-inline">
      掲載している金額は総務省「家計調査」の公表値です。
    </p>
  </div>

  <div class="card-soft p-3 p-md-4 mb-4">
    <h2 class="h6 mb-3">二人以上の世帯の1か月あたり平均支出（{{ \App\Support\Kakei::surveyYear() }}年・全国）</h2>
    <div class="row g-2">
      @foreach($items as $item)
        @continue($item['key'] === 'total')
        <div class="col-6 col-md-4 col-lg-3">
          <a href="{{ route('items.show', $item['key']) }}" class="d-block p-2 text-decoration-none card-soft h-100">
            <div class="small text-muted">{{ $item['name'] }}</div>
            <div class="fw-bold">{{ number_format($national[$item['key']]) }}<span class="small fw-normal">円</span></div>
          </a>
        </div>
      @endforeach
    </div>
    <p class="small text-muted mt-3 mb-0">
      消費支出の合計は <strong>{{ number_format($national['total']) }}円</strong>（平均世帯人員 {{ \App\Support\Kakei::data()['nationalHouseholdSize'] }}人）。
      費目名をおすと、52都市の中での順位を見られます。
    </p>
  </div>

  <div class="card-soft p-3 p-md-4 mb-4">
    <h2 class="h6">住んでいる街の平均と比べる</h2>
    <p class="small text-muted">県庁所在市など52都市について、費目別の平均支出を掲載しています。</p>
    <a href="{{ route('cities.index') }}" class="btn btn-sm btn-outline-success">都市別データを見る</a>
  </div>

  <h2 class="h5 mt-5 mb-3">記事</h2>
  <div class="row g-3">
    @foreach($articles as $article)
      <div class="col-12 col-md-6">
        <a href="{{ route('articles.show', $article['slug']) }}" class="d-block p-3 h-100 text-decoration-none card-soft">
          <div class="small text-muted">{{ $article['category'] }}・{{ $article['reading_minutes'] }}分で読めます</div>
          <div class="fw-semibold">{{ $article['title'] }}</div>
          <div class="small text-muted mt-1">{{ \Illuminate\Support\Str::limit($article['description'], 70) }}</div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
