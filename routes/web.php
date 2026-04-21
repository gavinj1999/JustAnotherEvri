<?php

use App\Http\Controllers\EvriScrapeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('scrapes', [EvriScrapeController::class, 'index'])->name('scrapes.index');
});

require __DIR__.'/settings.php';
