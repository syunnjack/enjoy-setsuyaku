<?php

namespace App\Http\Controllers;

use App\Support\Articles;
use App\Support\Kakei;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function home()
    {
        return view('home', [
            'articles' => Articles::all(),
            'national' => Kakei::national(),
            'items' => Kakei::items(),
        ]);
    }

    public function articles()
    {
        return view('articles.index', [
            'categories' => Articles::categories(),
            'total' => Articles::all()->count(),
        ]);
    }

    public function article(string $slug)
    {
        $article = Articles::find($slug);

        if ($article === null) {
            abort(404);
        }

        return view('articles.show', [
            'article' => $article,
            'related' => Articles::related($article),
        ]);
    }

    public function cities()
    {
        return view('data.cities', [
            'cities' => Kakei::cities()->sortByDesc(fn ($city) => $city['spending']['total'])->values(),
            'national' => Kakei::national(),
        ]);
    }

    public function city(string $slug)
    {
        $city = Kakei::city($slug);

        if ($city === null) {
            abort(404);
        }

        return view('data.city', [
            'city' => $city,
            'national' => Kakei::national(),
            'items' => Kakei::items(),
            'rank' => Kakei::rankOf($city),
            'cityCount' => Kakei::ranking('total')->count(),
            'perPerson' => Kakei::perPerson($city),
            'nationalPerPerson' => Kakei::nationalPerPerson(),
            'notable' => Kakei::notableItems($city),
            'nearest' => Kakei::nearestCities($city),
        ]);
    }

    public function item(string $key)
    {
        $name = Kakei::itemName($key);

        if ($name === null) {
            abort(404);
        }

        return view('data.item', [
            'key' => $key,
            'name' => $name,
            'ranking' => Kakei::ranking($key),
            'national' => Kakei::national(),
        ]);
    }

    public function sitemap()
    {
        $xml = Cache::remember('sitemap-xml', now()->addHour(), fn () => view('sitemap', [
            'articles' => Articles::all(),
            'cities' => Kakei::cities(),
            'items' => Kakei::items(),
        ])->render());

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
