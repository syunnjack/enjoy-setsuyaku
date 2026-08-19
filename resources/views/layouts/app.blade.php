<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#2f6f4e">

  <title>@yield('title', config('app.name').' | 家計調査で見る、くらしの支出と節約')</title>
  <meta name="description" content="@yield('description', '総務省の家計調査をもとに、費目別・都市別の平均支出を掲載しています。数字を確かめてから節約の順番を決められます。')">

  @php
      $canonicalQuery = array_filter(request()->only(['page']), fn ($value) => $value !== null && $value !== '' && $value !== '1');
      $canonicalUrl = url()->current().($canonicalQuery ? '?'.http_build_query($canonicalQuery) : '');
  @endphp
  <link rel="canonical" href="{{ $canonicalUrl }}">

  <meta property="og:site_name" content="{{ config('app.name') }}">
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:title" content="@yield('title', config('app.name'))">
  <meta property="og:description" content="@yield('description', '総務省の家計調査をもとに、費目別・都市別の平均支出を掲載しています。')">
  <meta property="og:url" content="{{ $canonicalUrl }}">
  <meta property="og:locale" content="ja_JP">
  <meta name="twitter:card" content="summary">

  @if(config('services.google_site_verification'))
  <meta name="google-site-verification" content="{{ config('services.google_site_verification') }}">
  @endif

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background:#fbfaf7; color:#20241f; font-family: system-ui, -apple-system, "Hiragino Sans", "Noto Sans JP", sans-serif; line-height:1.85; }
    a { color:#2f6f4e; }
    .article-body h2 { font-size:1.35rem; font-weight:700; margin:2.2rem 0 .8rem; padding-left:.6rem; border-left:5px solid #2f6f4e; }
    .article-body h3 { font-size:1.12rem; font-weight:700; margin:1.6rem 0 .6rem; }
    .article-body p { margin-bottom:1.1rem; }
    .article-body ul, .article-body ol { margin-bottom:1.1rem; }
    .article-body table { width:100%; margin-bottom:1.2rem; border-collapse:collapse; }
    .article-body th, .article-body td { border:1px solid #e2ded4; padding:.5rem .7rem; }
    .article-body th { background:#f3f1ea; }
    .article-body blockquote { border-left:4px solid #d8d3c6; padding:.4rem 0 .4rem 1rem; color:#555; }
    .card-soft { background:#fff; border:1px solid #e8e4da; border-radius:.6rem; }
    .card-soft:hover { border-color:#b9cdbf; }
  </style>
  @stack('structured-data')

  @if(config('services.ga_measurement_id'))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga_measurement_id') }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ config('services.ga_measurement_id') }}');
  </script>
  @endif

  @if(config('services.adsense_client'))
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('services.adsense_client') }}" crossorigin="anonymous"></script>
  @endif
</head>
<body>
<header class="bg-white border-bottom">
  <div class="container py-3">
    <div class="d-flex justify-content-between align-items-center">
      <a href="{{ route('home') }}" class="fs-5 fw-bold text-decoration-none">🌱 {{ config('app.name') }}</a>
      <nav class="small">
        <a href="{{ route('articles.index') }}" class="me-3">記事</a>
        <a href="{{ route('cities.index') }}" class="me-3">都市別データ</a>
        <a href="{{ route('about') }}">運営者情報</a>
      </nav>
    </div>
  </div>
</header>

@yield('content')

<footer class="bg-white border-top mt-5 py-4">
  <div class="container small text-muted">
    <p class="mb-2">
      <a href="{{ route('about') }}" class="me-3">運営者情報</a>
      <a href="{{ route('privacy') }}" class="me-3">プライバシーポリシー</a>
      <a href="{{ route('terms') }}" class="me-3">免責事項</a>
      <a href="{{ route('contact') }}">お問い合わせ</a>
    </p>
    <p class="mb-1">
      支出の数字は {{ \App\Support\Kakei::sourceLabel() }} をもとにしています。
    </p>
    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
  </div>
</footer>
</body>
</html>
