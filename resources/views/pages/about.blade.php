@extends('layouts.app')

@section('title', '運営者情報 | '.config('app.name'))
@section('description', config('app.name').'の運営方針、掲載しているデータの出所、記事の書き方について説明しています。')

@section('content')
<div class="container my-4" style="max-width: 720px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">運営者情報</span></nav>

  <h1 class="h4 fw-bold mb-4">運営者情報</h1>

  <section class="mb-4">
    <h2 class="h6">このサイトについて</h2>
    <p class="small">
      「{{ config('app.name') }}」は、家計の見直しを「数字を確かめてから」始められるようにするサイトです。
      総務省の家計調査をもとに、費目別・都市別の1か月あたり平均支出を掲載し、
      そこから何をどの順番で見直すかを記事で説明しています。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">運営者</h2>
    <table class="table table-sm">
      <tbody>
        <tr><th class="text-muted small" style="width:9rem;">サイト名</th><td>{{ config('app.name') }}</td></tr>
        <tr><th class="text-muted small">URL</th><td>{{ url('/') }}</td></tr>
        <tr><th class="text-muted small">運営者</th><td>知多丸</td></tr>
        <tr><th class="text-muted small">連絡先</th><td><a href="{{ route('contact') }}">お問い合わせページ</a>をご覧ください</td></tr>
        <tr><th class="text-muted small">開設</th><td>2026年8月</td></tr>
      </tbody>
    </table>
  </section>

  <section class="mb-4">
    <h2 class="h6">記事の書き方について</h2>
    <ul class="small">
      <li>金額や制度の内容は、公的機関が公表している資料にもとづいて書き、記事の中で出典を示します。</li>
      <li>「必ず◯円下がる」といった断定はしません。条件によって結果が変わるものは、変わる理由を書きます。</li>
      <li>体験談や実測値を書く場合は、それが個人の記録であることを明記します。</li>
      <li>誤りが分かった場合は本文を直し、更新日を記事に表示します。</li>
    </ul>
  </section>

  <section class="mb-4">
    <h2 class="h6">掲載データの出所</h2>
    <p class="small">
      支出の金額は <a href="{{ \App\Support\Kakei::sourceUrl() }}" rel="nofollow noopener" target="_blank">{{ \App\Support\Kakei::sourceLabel() }}</a> の公表値です。
      加工しているのは「全国平均との差を引き算する」ところまでで、推計や補間は行っていません。
    </p>
  </section>

  <a href="{{ route('home') }}" class="d-block text-center text-muted mt-4">トップページに戻る</a>
</div>
@endsection
