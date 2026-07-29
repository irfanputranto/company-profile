<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\defer;

class TrackPageVisit
{
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

        $userAgent = (string) $request->userAgent();
        $isBot = preg_match('/bot|crawl|spider|slurp|preview|headless/i', $userAgent) === 1;
        $applicationKey = (string) config('app.key');
        $sessionId = $request->hasSession() ? $request->session()->getId() : '';
        $referrerHost = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
        $countryCode = Str::upper((string) $request->headers->get('cf-ipcountry'));

        defer(function () use (
            $applicationKey,
            $countryCode,
            $isBot,
            $referrerHost,
            $request,
            $sessionId,
            $userAgent,
        ): void {
            PageVisit::query()->insert([
                'scope_type' => 'site',
                'scope_id' => 0,
                'route_name' => $request->route()?->getName(),
                'path' => '/'.ltrim($request->path(), '/'),
                'visitor_hash' => hash_hmac('sha256', "{$request->ip()}|{$userAgent}", $applicationKey),
                'session_hash' => $sessionId !== ''
                    ? hash_hmac('sha256', $sessionId, $applicationKey)
                    : null,
                'referrer_host' => is_string($referrerHost) ? Str::limit($referrerHost, 255, '') : null,
                'device_type' => $this->deviceType($userAgent),
                'country_code' => preg_match('/^[A-Z]{2}$/', $countryCode) === 1 ? $countryCode : null,
                'is_bot' => $isBot,
                'occurred_at' => now(),
            ]);
        })->name('record-page-visit');

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->user() !== null) {
            return false;
        }

        if ($request->route()?->named('login') || $request->is('up')) {
            return false;
        }

        return $response->isSuccessful()
            && str_contains((string) $response->headers->get('content-type'), 'text/html');
    }

    private function deviceType(string $userAgent): string
    {
        return match (true) {
            preg_match('/tablet|ipad/i', $userAgent) === 1 => 'tablet',
            preg_match('/mobile|android|iphone/i', $userAgent) === 1 => 'mobile',
            default => 'desktop',
        };
    }
}
