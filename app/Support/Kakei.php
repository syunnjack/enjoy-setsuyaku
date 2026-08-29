<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * 総務省「家計調査」から取り出した、都市別・費目別の1か月あたり平均支出。
 *
 * 数字は公表値をそのまま持っている。推計も補間もしない。
 * 元データは scripts/build-kakei-data.py が書き出す。
 */
class Kakei
{
    public static function data(): array
    {
        return Cache::remember('kakei', now()->addHour(), function () {
            $path = database_path('data/kakei-2024.json');

            if (! File::exists($path)) {
                throw new RuntimeException('database/data/kakei-2024.json が見つかりません。');
            }

            return json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        });
    }

    public static function national(): array
    {
        return self::data()['national'];
    }

    public static function items(): Collection
    {
        return collect(self::data()['items']);
    }

    public static function itemName(string $key): ?string
    {
        return self::items()->firstWhere('key', $key)['name'] ?? null;
    }

    public static function cities(): Collection
    {
        return collect(self::data()['cities']);
    }

    public static function city(string $slug): ?array
    {
        return self::cities()->firstWhere('slug', $slug);
    }

    /** ある費目について、支出の多い順に都市を並べる。 */
    public static function ranking(string $key): Collection
    {
        return self::cities()
            ->filter(fn (array $city) => $city['spending'][$key] !== null)
            ->sortByDesc(fn (array $city) => $city['spending'][$key])
            ->values();
    }

    /** 全国平均との差。プラスなら平均より多い。 */
    public static function differenceFromNational(array $city, string $key): ?int
    {
        $value = $city['spending'][$key] ?? null;
        $national = self::national()[$key] ?? null;

        if ($value === null || $national === null) {
            return null;
        }

        return $value - $national;
    }

    /** 総支出の多い順に並べたときの順位。1から数える。 */
    public static function rankOf(array $city, string $key = 'total'): ?int
    {
        $index = self::ranking($key)->search(fn (array $c) => $c['slug'] === $city['slug']);

        return $index === false ? null : $index + 1;
    }

    /** 平均世帯人員で割った、1人あたりの支出。 */
    public static function perPerson(array $city, string $key = 'total'): ?int
    {
        $value = $city['spending'][$key] ?? null;
        $size = $city['householdSize'] ?? null;

        if ($value === null || ! $size) {
            return null;
        }

        return (int) round($value / $size);
    }

    /** 全国平均の1人あたり支出。世帯人員の違いをならして比べるために使う。 */
    public static function nationalPerPerson(string $key = 'total'): ?int
    {
        $value = self::national()[$key] ?? null;
        $size = self::data()['nationalHouseholdSize'] ?? null;

        if ($value === null || ! $size) {
            return null;
        }

        return (int) round($value / $size);
    }

    /**
     * 全国平均との差が大きい費目。total は合計なので除く。
     * 返すのは ['key' => ..., 'name' => ..., 'diff' => ...] の配列。
     */
    public static function notableItems(array $city, int $take = 3): array
    {
        $diffs = self::items()
            ->reject(fn (array $item) => $item['key'] === 'total')
            ->map(fn (array $item) => [
                'key' => $item['key'],
                'name' => $item['name'],
                'diff' => self::differenceFromNational($city, $item['key']),
            ])
            ->filter(fn (array $row) => $row['diff'] !== null)
            ->sortByDesc('diff')
            ->values();

        return [
            'higher' => $diffs->take($take)->filter(fn ($r) => $r['diff'] > 0)->values()->all(),
            'lower' => $diffs->reverse()->take($take)->filter(fn ($r) => $r['diff'] < 0)->values()->all(),
        ];
    }

    /** 総支出が近い都市。比べる相手として示す。 */
    public static function nearestCities(array $city, int $take = 5): Collection
    {
        $total = $city['spending']['total'] ?? null;

        if ($total === null) {
            return collect();
        }

        return self::cities()
            ->reject(fn (array $c) => $c['slug'] === $city['slug'])
            ->filter(fn (array $c) => $c['spending']['total'] !== null)
            ->sortBy(fn (array $c) => abs($c['spending']['total'] - $total))
            ->take($take)
            ->values();
    }

    public static function sourceLabel(): string
    {
        return self::data()['sourceLabel'];
    }

    public static function sourceUrl(): string
    {
        return self::data()['sourceUrl'];
    }

    public static function surveyYear(): int
    {
        return self::data()['surveyYear'];
    }
}
