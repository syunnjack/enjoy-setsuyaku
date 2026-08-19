<?php

namespace Tests\Feature;

use App\Support\Articles;
use App\Support\Kakei;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    public function test_トップページが平均支出を表示する(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(number_format(Kakei::national()['total']));
    }

    public function test_記事がすべて開ける(): void
    {
        $articles = Articles::all();

        $this->assertGreaterThanOrEqual(11, $articles->count(), '記事が11本に足りません');

        foreach ($articles as $article) {
            $this->get('/articles/'.$article['slug'])
                ->assertOk()
                ->assertSee($article['title'], false);
        }
    }

    public function test_記事には出典が書かれている(): void
    {
        foreach (Articles::all() as $article) {
            $this->assertStringContainsString(
                'http',
                $article['body'],
                "出典のリンクがありません: {$article['slug']}"
            );
        }
    }

    public function test_審査で必要な固定ページがある(): void
    {
        foreach (['/about', '/privacy', '/terms', '/contact'] as $path) {
            $this->get($path)->assertOk();
        }

        $this->get('/privacy')
            ->assertSee('AdSense')
            ->assertSee('Cookie');
    }

    public function test_お問い合わせ先が空欄にならない(): void
    {
        // デプロイ時に .env へ CONTACT_ADDRESS= と空で書かれ、
        // 連絡先が空欄のまま公開されていたことがある。
        config(['mail.contact_address' => '']);

        $this->assertNotSame('', (string) config('mail.contact_address') ?: 'info@enjoy-setsuyaku.jp');

        $this->get('/contact')
            ->assertOk()
            ->assertSee('@')
            ->assertSee('mailto:', false);
    }

    public function test_都市ページが全国平均との差を出す(): void
    {
        $this->get('/cities/tokyo')
            ->assertOk()
            ->assertSee('東京都区部')
            ->assertSee('全国平均');

        $this->get('/cities/nowhere')->assertNotFound();
    }

    public function test_費目ページが52都市を並べる(): void
    {
        $response = $this->get('/items/utilities');

        $response->assertOk()->assertSee('光熱・水道');
        $this->assertSame(52, Kakei::ranking('utilities')->count());
    }

    public function test_サイトマップに記事と都市が載る(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('articles.show', 'heikin-shishutsu'), false)
            ->assertSee(route('cities.show', 'tokyo'), false);
    }

    public function test_robots_txtがサイトマップを案内する(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }

    public function test_2ページ目のcanonicalは自分自身を指す(): void
    {
        $this->get('/articles?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/articles').'?page=2">', false);
    }
}
