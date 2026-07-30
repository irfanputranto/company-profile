<?php

use App\Modules\CompanyProfile\Controllers\PublicAnalyticsController;
use App\Modules\CompanyProfile\Controllers\PublicBlogController;
use App\Modules\CompanyProfile\Controllers\PublicPricingController;
use App\Modules\CompanyProfile\Controllers\PublicProfileController;
use App\Modules\CompanyProfile\Controllers\PublicProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicProfileController::class)->name('home');
Route::get('/projects', PublicProjectController::class)->name('projects.index');
Route::get('/blog', [PublicBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{article:slug}', [PublicBlogController::class, 'show'])->name('blog.show');
Route::get('/pricing', PublicPricingController::class)->name('pricing.index');
Route::post('/analytics/events', PublicAnalyticsController::class)
    ->middleware('throttle:120,1')
    ->name('analytics.events.store');

Route::get('/lang/{language:code}', PublicProfileController::class)
    ->name('localized-home');
