<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', (string) config('security.headers.permissions_policy'));
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $headers->remove('X-Powered-By');

        if ($request->isSecure() && config('security.headers.hsts')) {
            $maxAge = max(0, (int) config('security.headers.hsts_max_age'));
            $headers->set('Strict-Transport-Security', "max-age={$maxAge}; includeSubDomains");
        }

        if (config('security.csp.enabled')) {
            $header = config('security.csp.report_only')
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';

            $headers->set($header, (string) config('security.csp.policy'));
        }

        if ($request->user() !== null || $request->is('login', 'register', 'forgot-password', 'reset-password/*')) {
            $headers->set('X-Robots-Tag', 'noindex, nofollow, noimageindex, noarchive, nosnippet');
            $headers->set('Cache-Control', 'no-store, private, max-age=0');
            $headers->set('Pragma', 'no-cache');
            $headers->set('Expires', '0');
        }

        return $response;
    }
}
