<?php

use App\Http\Middleware\ApplyLocale;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrackPageVisit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(app_path('Modules/Auth/Routes/web.php'));

            Route::middleware('web')
                ->group(app_path('Modules/Master/Routes/web.php'));

            Route::middleware('web')
                ->group(app_path('Modules/System/Routes/web.php'));

            Route::middleware('web')
                ->group(app_path('Modules/CompanyProfile/Routes/web.php'));

            Route::middleware('web')
                ->group(app_path('Modules/ProjectManagement/Routes/web.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustHosts(
            at: static fn (): array => config('security.trusted_hosts'),
            subdomains: false,
        );
        $middleware->append(SecurityHeaders::class);
        $middleware->appendToGroup('web', [
            ApplyLocale::class,
            TrackPageVisit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $exception): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
