<?php

use App\Modules\ProjectManagement\Controllers\ClientCompanyController;
use App\Modules\ProjectManagement\Controllers\ManagedProjectController;
use App\Modules\ProjectManagement\Controllers\ProjectDocumentController;
use App\Modules\ProjectManagement\Controllers\ProjectFeatureController;
use App\Modules\ProjectManagement\Controllers\ProjectNotificationController;
use App\Modules\ProjectManagement\Controllers\ProjectPhaseController;
use App\Modules\ProjectManagement\Controllers\ProjectServerController;
use App\Modules\ProjectManagement\Controllers\ProjectTechnologyController;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('project-management')->name('project-management.')->scopeBindings()->group(function (): void {
    $resource = function (string $uri, string $controller, string $permissionResource): PendingResourceRegistration {
        return Route::resource($uri, $controller)
            ->middlewareFor('index', "can:view_{$permissionResource}")
            ->middlewareFor('show', "can:show_{$permissionResource}")
            ->middlewareFor(['create', 'store'], "can:create_{$permissionResource}")
            ->middlewareFor(['edit', 'update'], "can:update_{$permissionResource}")
            ->middlewareFor('destroy', "can:delete_{$permissionResource}");
    };

    $resource('companies', ClientCompanyController::class, 'client_companies')->except(['show']);
    $resource('projects', ManagedProjectController::class, 'managed_projects')
        ->parameters(['projects' => 'managed_project']);

    Route::prefix('projects/{managed_project}')->name('projects.')->group(function (): void {
        Route::get('board', [ManagedProjectController::class, 'board'])->middleware('can:show_managed_projects')->name('board');
        Route::post('documents', [ProjectDocumentController::class, 'store'])->middleware('can:update_managed_projects')->name('documents.store');
        Route::get('documents/{document}/download', [ProjectDocumentController::class, 'download'])->middleware('can:show_managed_projects')->name('documents.download');
        Route::delete('documents/{document}', [ProjectDocumentController::class, 'destroy'])->middleware('can:update_managed_projects')->name('documents.destroy');

        Route::post('phases', [ProjectPhaseController::class, 'store'])->middleware('can:update_managed_projects')->name('phases.store');
        Route::put('phases/{phase}', [ProjectPhaseController::class, 'update'])->middleware('can:update_managed_projects')->name('phases.update');
        Route::delete('phases/{phase}', [ProjectPhaseController::class, 'destroy'])->middleware('can:update_managed_projects')->name('phases.destroy');
        Route::post('phases/{phase}/features', [ProjectFeatureController::class, 'store'])->middleware('can:update_managed_projects')->name('phases.features.store');
        Route::put('phases/{phase}/features/{feature}', [ProjectFeatureController::class, 'update'])->middleware('can:update_managed_projects')->name('phases.features.update');
        Route::patch('phases/{phase}/features/{feature}/move', [ProjectFeatureController::class, 'move'])->middleware('can:update_managed_projects')->name('phases.features.move');
        Route::delete('phases/{phase}/features/{feature}', [ProjectFeatureController::class, 'destroy'])->middleware('can:update_managed_projects')->name('phases.features.destroy');

        Route::post('technologies', [ProjectTechnologyController::class, 'store'])->middleware('can:update_managed_projects')->name('technologies.store');
        Route::delete('technologies/{technology}', [ProjectTechnologyController::class, 'destroy'])->middleware('can:update_managed_projects')->name('technologies.destroy');

        Route::post('servers', [ProjectServerController::class, 'store'])->middleware('can:update_managed_projects')->name('servers.store');
        Route::put('servers/{server}', [ProjectServerController::class, 'update'])->middleware('can:update_managed_projects')->name('servers.update');
        Route::get('servers/{server}/credentials', [ProjectServerController::class, 'credentials'])->middleware('can:show_project_credentials')->name('servers.credentials');
        Route::delete('servers/{server}', [ProjectServerController::class, 'destroy'])->middleware('can:update_managed_projects')->name('servers.destroy');
    });

    Route::post('notifications/{notification}/read', [ProjectNotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/read-all', [ProjectNotificationController::class, 'readAll'])->name('notifications.read-all');
});
