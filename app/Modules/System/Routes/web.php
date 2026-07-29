<?php

use App\Modules\System\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:view_activity_logs'])
    ->prefix('system')
    ->name('system.')
    ->group(function (): void {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activity}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
