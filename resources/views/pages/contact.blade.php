@extends('layouts.app')

@section('title', 'お問い合わせ | '.config('app.name'))
@section('description', config('app.name').'へのお問い合わせ方法です。掲載内容の誤りのご指摘もこちらへお願いします。')

@section('content')
<div class="container my-4" style="max-width: 720px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">お問い合わせ</span></nav>

  <h1 class="h4 fw-bold mb-4">お問い合わせ</h1>

  <p class="small">
    掲載内容の誤りのご指摘、取材・掲載のご相談は、下記のメールアドレスへお送りください。
    数日以内に返信いたします。
  </p>

  <div class="card-soft p-3 mb-4">
    <div class="small text-muted">メールアドレス</div>
    <div class="fs-5">{{ config('mail.contact_address') }}</div>
  </div>

  <h2 class="h6">お送りいただく際のお願い</h2>
  <ul class="small">
    <li>掲載内容の誤りをご指摘いただく場合は、該当ページのURLをお書き添えください。</li>
    <li>個別の家計相談・税務相談にはお答えできません。</li>
    <li>いただいたメールアドレスは、返信の目的にのみ使用します（<a href="{{ route('privacy') }}">プライバシーポリシー</a>）。</li>
  </ul>

  <a href="{{ route('home') }}" class="d-block text-center text-muted mt-4">トップページに戻る</a>
</div>
@endsection
