<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApplyLocale
{
    public function __construct(private LanguageResolver $resolver) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usesLanguageAsLocale = $request->routeIs('localized-home', 'locale.switch');
        $routeLanguage = $usesLanguageAsLocale ? $request->route('language') : null;
        $routeLocale = $routeLanguage instanceof Language
            ? $routeLanguage->code
            : (is_string($routeLanguage) ? $routeLanguage : null);

        if ($routeLocale !== null) {
            abort_unless($this->resolver->activeLanguages()->contains('code', $routeLocale), 404);
        }

        $requestedLocale = $routeLocale ?? $request->session()->get('site_locale');
        $locale = $this->resolver->resolve(is_string($requestedLocale) ? $requestedLocale : null);

        App::setLocale($locale);
        $request->session()->put('site_locale', $locale);

        return $next($request);
    }
}
