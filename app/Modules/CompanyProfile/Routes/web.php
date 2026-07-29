<?php

use App\Modules\CompanyProfile\Controllers\AnalyticsController;
use App\Modules\CompanyProfile\Controllers\ContentController;
use App\Modules\CompanyProfile\Controllers\LanguageController;
use App\Modules\CompanyProfile\Controllers\LocaleController;
use App\Modules\CompanyProfile\Controllers\MediaController;
use App\Modules\CompanyProfile\Controllers\TranslationController;
use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use App\Modules\CompanyProfile\Support\TranslatableContentRegistry;
use Illuminate\Support\Facades\Route;

Route::post('locale/{language:code}', LocaleController::class)->name('locale.switch');

Route::middleware('auth')->prefix('company-profile')->name('company-profile.')->group(function (): void {
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::resource('media', MediaController::class)->except(['show']);
    Route::resource('languages', LanguageController::class)->except(['show']);

    Route::prefix('translations/{resource}/{record}')
        ->where([
            'resource' => implode('|', array_filter(
                ContentResourceRegistry::keys(),
                TranslatableContentRegistry::supports(...),
            )),
            'record' => '[0-9]+',
        ])
        ->name('translations.')
        ->controller(TranslationController::class)
        ->group(function (): void {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

    Route::prefix('content/{resource}')
        ->where(['resource' => ContentResourceRegistry::routeConstraint()])
        ->name('content.')
        ->controller(ContentController::class)
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{record}/edit', 'editRecord')->whereNumber('record')->name('edit');
            Route::put('{record}', 'updateRecord')->whereNumber('record')->name('update');
            Route::delete('{record}', 'destroyRecord')->whereNumber('record')->name('destroy');
        });
});
