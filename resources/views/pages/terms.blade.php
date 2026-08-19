@extends('layouts.app')

@section('title', '免責事項 | '.config('app.name'))
@section('description', config('app.name').'に掲載している情報の取り扱いと、著作権・リンクについての方針です。')

@section('content')
<div class="container my-4" style="max-width: 720px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">免責事項</span></nav>

  <h1 class="h4 fw-bold mb-4">免責事項</h1>

  <section class="mb-4">
    <h2 class="h6">掲載内容について</h2>
    <p class="small">
      当サイトは、公的機関が公表している資料をもとに情報を掲載しています。掲載時点では正確を期していますが、
      制度・料金・統計は改定されることがあり、内容の正確性・完全性を保証するものではありません。
      実際の手続きや契約の前には、記事中でリンクしている一次情報をご確認ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">損害について</h2>
    <p class="small">
      当サイトの情報を利用したことによって生じた損害について、運営者は責任を負いかねます。
      節約の効果は住まい・世帯人数・契約内容によって変わります。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">専門的な判断について</h2>
    <p class="small">
      当サイトは、税務・法律・保険・投資に関する個別の助言を行うものではありません。
      個別の事情に応じた判断が必要な場合は、税理士・社会保険労務士などの専門家にご相談ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">著作権</h2>
    <p class="small">
      当サイトの文章の著作権は運営者に帰属します。引用の範囲を超えた転載はご遠慮ください。
      統計データの著作権は、それぞれの公表機関に帰属します。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">リンク</h2>
    <p class="small">
      当サイトへのリンクは自由です。リンク先のサイトで提供される情報については、当サイトは責任を負いかねます。
    </p>
  </section>

  <a href="{{ route('home') }}" class="d-block text-center text-muted mt-4">トップページに戻る</a>
</div>
@endsection
