@extends('layouts.app')

@section('title', $city['name'].'の平均支出は月'.number_format($city['spending']['total']).'円 | '.config('app.name'))
@section('description', $city['name'].'（'.$city['prefecture'].'）の二人以上の世帯の消費支出は月平均'.number_format($city['spending']['total']).'円。費目別に全国平均と比べられます。出典は総務省「家計調査」。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
  '@@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => url('/')],
      ['@type' => 'ListItem', 'position' => 2, 'name' => '都市別データ', 'item' => route('cities.index')],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $city['name'], 'item' => route('cities.show', $city['slug'])],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container my-4" style="max-width: 760px;">
  <nav class="small mb-3">
    <a href="{{ route('home') }}">トップ</a> <span class="text-muted mx-1">/</span>
    <a href="{{ route('cities.index') }}">都市別データ</a> <span class="text-muted mx-1">/</span>
    <span class="text-muted">{{ $city['name'] }}</span>
  </nav>

  <h1 class="h4 fw-bold">{{ $city['name'] }}の平均支出</h1>
  <p class="text-muted">
    {{ $city['prefecture'] }}・二人以上の世帯・1か月あたり（{{ \App\Support\Kakei::surveyYear() }}年）<br>
    平均世帯人員 {{ $city['householdSize'] }}人
  </p>

  <div class="table-responsive">
    <table class="table align-middle bg-white">
      <thead>
        <tr><th>費目</th><th class="text-end">{{ $city['name'] }}</th><th class="text-end">全国平均</th><th class="text-end">差</th></tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          @php
              $value = $city['spending'][$item['key']];
              $diff = $value === null ? null : $value - $national[$item['key']];
          @endphp
          <tr class="{{ $item['key'] === 'total' ? 'fw-bold' : '' }}">
            <td>
              @if($item['key'] === 'total')
                {{ $item['name'] }}
              @else
                <a href="{{ route('items.show', $item['key']) }}">{{ $item['name'] }}</a>
              @endif
            </td>
            <td class="text-end">{{ $value === null ? '—' : number_format($value).'円' }}</td>
            <td class="text-end text-muted">{{ number_format($national[$item['key']]) }}円</td>
            <td class="text-end {{ $diff === null ? '' : ($diff >= 0 ? 'text-danger' : 'text-success') }}">
              {{ $diff === null ? '—' : ($diff >= 0 ? '+' : '').number_format($diff).'円' }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <p class="small text-muted">
    出典: <a href="{{ \App\Support\Kakei::sourceUrl() }}" rel="nofollow noopener" target="_blank">{{ \App\Support\Kakei::sourceLabel() }}</a><br>
    平均世帯人員・持ち家率・気候が都市ごとに違うため、差がそのまま「使いすぎ」を意味するわけではありません。
    差の大きい費目から、契約内容や使い方を見直す手がかりとしてお使いください。
  </p>

  <p class="mt-3"><a href="{{ route('cities.index') }}">ほかの都市の平均を見る</a></p>
</div>
@endsection
