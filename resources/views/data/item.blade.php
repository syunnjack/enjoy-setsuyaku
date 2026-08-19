@extends('layouts.app')

@section('title', $name.'の平均支出が多い都市ランキング | '.config('app.name'))
@section('description', $name.'の1か月あたり平均支出を、52都市について多い順に並べました。全国平均は'.number_format($national[$key]).'円。出典は総務省「家計調査」。')

@section('content')
<div class="container my-4" style="max-width: 760px;">
  <nav class="small mb-3">
    <a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span>
    <a href="{{ route('cities.index') }}">都市別データ</a> <span class="text-muted mx-1">/</span>
    <span class="text-muted">{{ $name }}</span>
  </nav>

  <h1 class="h4 fw-bold">{{ $name }}の平均支出（都市別）</h1>
  <p class="text-muted">
    二人以上の世帯・1か月あたり（{{ \App\Support\Kakei::surveyYear() }}年）。全国平均は {{ number_format($national[$key]) }}円です。
  </p>

  <div class="table-responsive">
    <table class="table table-sm align-middle bg-white">
      <thead><tr><th>順位</th><th>都市</th><th class="text-end">{{ $name }}</th><th class="text-end">全国平均との差</th></tr></thead>
      <tbody>
        @foreach($ranking as $index => $city)
          @php $diff = $city['spending'][$key] - $national[$key]; @endphp
          <tr>
            <td class="text-muted">{{ $index + 1 }}</td>
            <td><a href="{{ route('cities.show', $city['slug']) }}">{{ $city['name'] }}</a></td>
            <td class="text-end">{{ number_format($city['spending'][$key]) }}円</td>
            <td class="text-end {{ $diff >= 0 ? 'text-danger' : 'text-success' }}">{{ $diff >= 0 ? '+' : '' }}{{ number_format($diff) }}円</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <p class="small text-muted">
    出典: <a href="{{ \App\Support\Kakei::sourceUrl() }}" rel="nofollow noopener" target="_blank">{{ \App\Support\Kakei::sourceLabel() }}</a>
  </p>
</div>
@endsection
