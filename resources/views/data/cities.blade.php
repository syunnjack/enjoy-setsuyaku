@extends('layouts.app')

@section('title', '都市別の平均支出（52都市） | '.config('app.name'))
@section('description', '県庁所在市など52都市の、1か月あたり消費支出の平均を多い順に並べました。出典は総務省「家計調査」。')

@section('content')
<div class="container my-4" style="max-width: 860px;">
  <nav class="small mb-3"><a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span> <span class="text-muted">都市別データ</span></nav>

  <h1 class="h4 fw-bold">都市別の平均支出</h1>
  <p class="text-muted">
    二人以上の世帯の1か月あたり消費支出（{{ \App\Support\Kakei::surveyYear() }}年）。全国平均は {{ number_format($national['total']) }}円です。
  </p>

  <div class="table-responsive">
    <table class="table table-sm align-middle bg-white">
      <thead>
        <tr><th>順位</th><th>都市</th><th class="text-end">消費支出</th><th class="text-end">全国平均との差</th></tr>
      </thead>
      <tbody>
        @foreach($cities as $index => $city)
          <tr>
            <td class="text-muted">{{ $index + 1 }}</td>
            <td><a href="{{ route('cities.show', $city['slug']) }}">{{ $city['name'] }}</a>
              <span class="small text-muted">{{ $city['prefecture'] }}</span></td>
            <td class="text-end">{{ number_format($city['spending']['total']) }}円</td>
            <td class="text-end {{ $city['spending']['total'] - $national['total'] >= 0 ? 'text-danger' : 'text-success' }}">
              {{ $city['spending']['total'] - $national['total'] >= 0 ? '+' : '' }}{{ number_format($city['spending']['total'] - $national['total']) }}円
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <p class="small text-muted">
    出典: <a href="{{ \App\Support\Kakei::sourceUrl() }}" rel="nofollow noopener" target="_blank">{{ \App\Support\Kakei::sourceLabel() }}</a><br>
    世帯人員や持ち家率が都市によって違うため、金額の大小がそのまま「暮らしやすさ」を表すわけではありません。
  </p>
</div>
@endsection
