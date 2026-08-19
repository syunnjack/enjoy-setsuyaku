@extends('layouts.app')

@section('title', 'プライバシーポリシー | '.config('app.name'))
@section('description', config('app.name').'における、アクセス解析・広告配信・Cookie・個人情報の取り扱いについて説明しています。')

@section('content')
<div class="container my-4" style="max-width: 720px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">プライバシーポリシー</span></nav>

  <h1 class="h4 fw-bold mb-4">プライバシーポリシー</h1>

  <section class="mb-4">
    <h2 class="h6">個人情報の取得</h2>
    <p class="small">
      当サイトは、閲覧するだけであれば氏名・住所・電話番号などの個人情報の入力を求めません。
      お問い合わせをいただいた場合に限り、返信のためにメールアドレスと本文をお預かりします。
      いただいた内容は返信の目的にのみ使い、ご本人の同意なく第三者へ提供することはありません。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">アクセス解析</h2>
    <p class="small">
      当サイトは、利用状況を把握するために Google アナリティクスを使用することがあります。
      これは Cookie を利用してデータを収集しますが、個人を特定する情報は含まれません。
      収集の仕組みと、収集を無効にする方法は
      <a href="https://policies.google.com/technologies/partner-sites?hl=ja" rel="nofollow noopener" target="_blank">Google のポリシーと規約</a>
      をご確認ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">広告配信について</h2>
    <p class="small">
      当サイトは、第三者配信の広告サービス（Google AdSense を含む）を利用することがあります。
      広告配信事業者は、利用者の興味に応じた広告を表示するために Cookie を使用することがあります。
    </p>
    <p class="small">
      パーソナライズ広告は
      <a href="https://myadcenter.google.com/" rel="nofollow noopener" target="_blank">Google の広告設定</a>
      から無効にできます。第三者配信事業者による Cookie の使用については
      <a href="https://policies.google.com/technologies/ads?hl=ja" rel="nofollow noopener" target="_blank">広告に関する Google のポリシー</a>
      をご覧ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">Cookie の無効化</h2>
    <p class="small">
      Cookie はブラウザの設定から無効にできます。無効にした場合でも当サイトの記事は読めますが、
      一部の表示や計測が正しく動作しないことがあります。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">アクセスログ</h2>
    <p class="small">
      サーバーには、アクセス日時・IPアドレス・閲覧ページ・ブラウザの種類などが記録されます。
      これは障害対応と不正アクセスの確認のために使用し、それ以外の目的では使用しません。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h6">改定</h2>
    <p class="small">
      本ポリシーは、法令の変更や取り扱いの見直しに応じて改定することがあります。
      重要な変更があった場合は、このページに掲載します。
    </p>
    <p class="small text-muted">制定: 2026年8月</p>
  </section>

  <a href="{{ route('home') }}" class="d-block text-center text-muted mt-4">トップページに戻る</a>
</div>
@endsection
