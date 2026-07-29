<?php

use App\Modules\CompanyProfile\Controllers\PublicProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicProfileController::class)->name('home');

Route::get('/lang/{language:code}', PublicProfileController::class)
    ->name('localized-home');
