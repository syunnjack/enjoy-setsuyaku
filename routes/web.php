<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/articles', [PageController::class, 'articles'])->name('articles.index');
Route::get('/articles/{slug}', [PageController::class, 'article'])->name('articles.show');

Route::get('/cities', [PageController::class, 'cities'])->name('cities.index');
Route::get('/cities/{slug}', [PageController::class, 'city'])->whereAlpha('slug')->name('cities.show');
Route::get('/items/{key}', [PageController::class, 'item'])->whereAlpha('key')->name('items.show');

Route::view('/about', 'pages.about')->name('about');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/contact', 'pages.contact')->name('contact');

Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');

Route::get('/robots.txt', function () {
    $body = "User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml')."\n";

    return response($body, 200, ['Content-Type' => 'text/plain']);
});
