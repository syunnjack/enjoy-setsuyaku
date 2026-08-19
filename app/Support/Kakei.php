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
