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
    出典: <a href="{{ \App\Support\Kakei::sourceUrl() }}" rel="nofollow noopener" target="_blank">{{ \App\Support\Kakei::sourceLabel() }}</a>
  </p>

  <h2 class="h5 fw-bold mt-4">この表から読み取れること</h2>

  @if($rank !== null)
    <p>
      {{ $city['name'] }}の消費支出は月{{ number_format($city['spending']['total']) }}円で、
      掲載している{{ $cityCount }}都市のうち<strong>{{ $rank }}番目</strong>に多い水準です。
      全国平均（{{ number_format($national['total']) }}円）との差は
      {{ ($city['spending']['total'] - $national['total']) >= 0 ? '+' : '' }}{{ number_format($city['spending']['total'] - $national['total']) }}円でした。
    </p>
  @endif

  @if(count($notable['higher']) > 0)
    <p>
      全国平均より多いのは、
      @foreach($notable['higher'] as $i => $row){{ $i > 0 ? '、' : '' }}<a href="{{ route('items.show', $row['key']) }}">{{ $row['name'] }}</a>（+{{ number_format($row['diff']) }}円）@endforeach
      です。
    </p>
  @endif

  @if(count($notable['lower']) > 0)
    <p>
      逆に少ないのは、
      @foreach($notable['lower'] as $i => $row){{ $i > 0 ? '、' : '' }}<a href="{{ route('items.show', $row['key']) }}">{{ $row['name'] }}</a>（{{ number_format($row['diff']) }}円）@endforeach
      です。
    </p>
  @endif

  @if($perPerson !== null && $nationalPerPerson !== null)
    <h2 class="h5 fw-bold mt-4">世帯人員をならして比べる</h2>
    <p>
      支出の総額は、世帯に何人いるかで大きく変わります。
      {{ $city['name'] }}の平均世帯人員は{{ $city['householdSize'] }}人、全国平均は{{ \App\Support\Kakei::data()['nationalHouseholdSize'] }}人です。
      1人あたりに直すと、{{ $city['name'] }}は月{{ number_format($perPerson) }}円、全国平均は月{{ number_format($nationalPerPerson) }}円になります。
      @php
        $totalAbove = $city['spending']['total'] > $national['total'];
        $perAbove = $perPerson > $nationalPerPerson;
      @endphp
      @if($perPerson === $nationalPerPerson)
        1人あたりでは全国平均とほぼ同じです。
      @elseif($totalAbove && $perAbove)
        総額でも1人あたりでも、全国平均を上回っています。
      @elseif(! $totalAbove && ! $perAbove)
        総額でも1人あたりでも、全国平均を下回っています。
      @elseif(! $totalAbove && $perAbove)
        <strong>総額では全国平均を下回りますが、世帯人員が少ないぶん、1人あたりでは上回っています。</strong>
        総額の少なさが、そのまま支出の少なさを意味していない例です。
      @else
        <strong>総額では全国平均を上回りますが、世帯人員が多いぶん、1人あたりでは下回っています。</strong>
        総額の多さが、そのまま支出の多さを意味していない例です。
      @endif
    </p>
  @endif

  @if($nearest->isNotEmpty())
    <h2 class="h5 fw-bold mt-4">支出の水準が近い都市</h2>
    <p>総額が近い都市と並べると、どの費目で違いが出ているかが分かります。</p>
    <ul>
      @foreach($nearest as $other)
        <li>
          <a href="{{ route('cities.show', $other['slug']) }}">{{ $other['name'] }}</a>
          … 月{{ number_format($other['spending']['total']) }}円（世帯人員 {{ $other['householdSize'] }}人）
        </li>
      @endforeach
    </ul>
  @endif

  <h2 class="h5 fw-bold mt-4">数字を見るときの注意</h2>
  <p class="small text-muted">
    平均世帯人員・持ち家率・気候が都市ごとに違うため、差がそのまま「使いすぎ」を意味するわけではありません。
    たとえば持ち家が多い地域は住居費が小さく出ますし、寒い地域は光熱・水道費が大きく出ます。
    差の大きい費目から、契約内容や使い方を見直す手がかりとしてお使いください。
  </p>

  <p class="mt-3"><a href="{{ route('cities.index') }}">ほかの都市の平均を見る</a></p>
</div>
@endsection
