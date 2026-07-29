<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SecureMediaController;
use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Auth\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');
    });

    Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::middleware('can:access adminpanel')->prefix('secure-media')->name('secure-media.')->group(function (): void {
        Route::get('users/{user}/avatar', [SecureMediaController::class, 'userAvatar'])
            ->whereUuid('user')
            ->name('users.avatar');
    });

    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('profile/photo', [ProfileController::class, 'updatePhoto'])
        ->middleware('throttle:10,1')
        ->name('profile.photo.update');

    Route::get('dashboard', DashboardController::class)->name('dashboard');
});
