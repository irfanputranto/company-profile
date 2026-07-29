<?php

namespace App\Http\Middleware;

use App\Modules\CompanyProfile\Services\VisitRecorder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\defer;

class TrackPageVisit
{
    public function __construct(private readonly VisitRecorder $visitRecorder) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $context = $this->visitRecorder->context($request);

        defer(
            fn () => $this->visitRecorder->record($context)
        )->name('record-page-visit');

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        $routeName = $request->route()?->getName();

        if (! is_string($routeName) || ! in_array($routeName, config('analytics.public_routes', []), true)) {
            return false;
        }

        return $response->isSuccessful()
            && str_contains((string) $response->headers->get('content-type'), 'text/html');
    }
}
