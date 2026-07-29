<?php

use App\Modules\Master\Permission\Controllers\PermissionController;
use App\Modules\Master\Role\Controllers\RoleController;
use App\Modules\Master\User\Controllers\UserController;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('master')->name('master.')->group(function (): void {
    $registerMaster = function (string $uri, string $controller, string $permissionResource): PendingResourceRegistration {
        return Route::resource($uri, $controller)
            ->except(['show'])
            ->middlewareFor('index', "can:view_{$permissionResource}")
            ->middlewareFor(['create', 'store'], "can:create_{$permissionResource}")
            ->middlewareFor(['edit', 'update'], "can:update_{$permissionResource}")
            ->middlewareFor('destroy', "can:delete_{$permissionResource}");
    };

    $registerMaster('users', UserController::class, 'users');
    $registerMaster('roles', RoleController::class, 'roles');
    $registerMaster('permissions', PermissionController::class, 'permissions');
});
