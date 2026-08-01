<?php

namespace App\Providers;

use App\Modules\CompanyProfile\Services\PublicContentPageNavigation;
use App\Modules\CompanyProfile\View\Components\LanguageSwitcher;
use App\Notifications\ProjectServerExpiryNotification;
use App\Services\PageGuideResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as IlluminateView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(PublicContentPageNavigation::class);
    }

    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/adminpanel'), 'adminpanel');
        Blade::component('language-switcher', LanguageSwitcher::class);
        TrustProxies::at(config('security.trusted_proxies'));

        if (parse_url((string) config('app.url'), PHP_URL_SCHEME) === 'https') {
            URL::forceHttps();
        }

        $pageGuideResolver = $this->app->make(PageGuideResolver::class);
        View::composer('adminpanel.layouts.header', function (IlluminateView $view) use ($pageGuideResolver): void {
            $view->with('pageGuide', $pageGuideResolver->resolve(request()->route()?->getName()));
            $user = auth()->user();
            $notifications = collect();
            $notificationsCount = 0;

            if ($user) {
                $notificationQuery = $user->unreadNotifications()->where('type', ProjectServerExpiryNotification::class);
                $notificationsCount = (clone $notificationQuery)->count();
                $notifications = (clone $notificationQuery)->latest()->limit(5)->get();
            }

            $view->with('projectNotifications', $notifications);
            $view->with('projectNotificationsCount', $notificationsCount);
        });

        View::composer(
            ['components.public.header', 'components.public.footer'],
            function (IlluminateView $view): void {
                $view->with(
                    'navigationPages',
                    $this->app->make(PublicContentPageNavigation::class)->pages(),
                );
            },
        );

        RateLimiter::for('login', function (Request $request): array {
            $login = Str::lower($request->string('login')->trim()->toString());
            $credentialKey = hash('sha256', $login.'|'.$request->ip());

            return [
                Limit::perMinute((int) config('security.login.attempts_per_minute'))
                    ->by('credential:'.$credentialKey),
                Limit::perMinute((int) config('security.login.ip_attempts_per_minute'))
                    ->by('ip:'.$request->ip()),
            ];
        });
    }
}
