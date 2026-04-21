<?php

use App\Http\Controllers\Api\EvriScrapeController;
use App\Http\Middleware\ScraperTokenAuth;
use Illuminate\Support\Facades\Route;

Route::middleware(ScraperTokenAuth::class)->group(function () {
    Route::post('evri/scrape-results', [EvriScrapeController::class, 'store'])
        ->name('api.evri.scrape-results.store');
});
