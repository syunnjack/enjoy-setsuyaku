<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * 記事は resources/articles/*.md に置いてある。
 *
 * データベースに入れていないのは、記事が git の履歴に残った方が
 * 直しやすいため。ファイルの先頭にタイトルなどを書いておき、
 * 本文は Markdown で書く。
 */
class Articles
{
    public static function all(): Collection
    {
        return Cache::remember('articles', now()->addMinutes(10), function () {
            $directory = resource_path('articles');

            if (! File::isDirectory($directory)) {
                return collect();
            }

            return collect(File::files($directory))
                ->filter(fn ($file) => $file->getExtension() === 'md')
                ->map(fn ($file) => self::parse($file->getPathname()))
                ->sortByDesc('published_on')
                ->values();
        });
    }

    public static function find(string $slug): ?array
    {
        return self::all()->firstWhere('slug', $slug);
    }

    /** 同じカテゴリの記事（自分を除く）。 */
    public static function related(array $article, int $limit = 4): Collection
    {
        return self::all()
            ->where('category', $article['category'])
            ->where('slug', '!=', $article['slug'])
            ->take($limit);
    }

    public static function categories(): Collection
    {
        return self::all()->groupBy('category');
    }

    private static function parse(string $path): array
    {
        $raw = File::get($path);

        if (! Str::startsWith($raw, '---')) {
            throw new RuntimeException("記事の先頭に情報がありません: {$path}");
        }

        [, $frontMatter, $body] = preg_split('/^---\s*$/m', $raw, 3);

        $meta = [];
        // \R をそのまま使うと、UTF-8指定なしでは単独バイトの 0x85 も改行と
        // みなされる。「公」(E5 85 AC) のような文字の途中で切れてしまうため、
        // 改行だけを明示して分ける。
        foreach (preg_split('/\r\n|\r|\n/', trim($frontMatter)) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $meta[trim($key)] = trim($value);
        }

        foreach (['title', 'description', 'category', 'published_on'] as $required) {
            if (empty($meta[$required])) {
                throw new RuntimeException("{$required} が書かれていません: {$path}");
            }
        }

        $meta['slug'] = basename($path, '.md');
        $meta['body'] = trim($body);
        $meta['html'] = Str::markdown($meta['body']);
        $meta['reading_minutes'] = max(1, (int) ceil(mb_strlen(strip_tags($meta['html'])) / 500));

        return $meta;
    }
}
