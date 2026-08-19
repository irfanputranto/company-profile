<?php

$applicationHost = parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';
$trustedHosts = array_filter(array_map('trim', explode(',', (string) env('TRUSTED_HOSTS', $applicationHost))));
$trustedProxies = array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', ''))));
$isLocal = config('app.env', 'production') === 'local';
$trustedImageSources = trim((string) env('SECURITY_CSP_IMAGE_SOURCES', 'https://cdn.flyonui.com https://sp.tinymce.com'));
$trustedScriptSources = trim((string) env(
    'SECURITY_CSP_SCRIPT_SOURCES',
    'https://cdn.jsdelivr.net https://static.cloudflareinsights.com',
));
$trustedStyleSources = trim((string) env(
    'SECURITY_CSP_STYLE_SOURCES',
    'https://cdn.jsdelivr.net https://fonts.googleapis.com',
));
$trustedFontSources = trim((string) env('SECURITY_CSP_FONT_SOURCES', 'https://fonts.gstatic.com'));
$trustedConnectSources = trim((string) env('SECURITY_CSP_CONNECT_SOURCES', 'https://cloudflareinsights.com'));
$viteHttpSources = $isLocal
    ? trim((string) env(
        'SECURITY_CSP_VITE_HTTP_SOURCES',
        'http://localhost:5173 http://127.0.0.1:5173 http://*:5173',
    ))
    : '';
$viteWebSocketSources = $isLocal
    ? trim((string) env(
        'SECURITY_CSP_VITE_WEBSOCKET_SOURCES',
        'ws://localhost:5173 ws://127.0.0.1:5173 ws://*:5173',
    ))
    : '';
$defaultCspPolicy = implode('; ', array_filter([
    "default-src 'self'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
    "object-src 'none'",
    trim("script-src 'self' 'unsafe-inline' 'unsafe-eval' {$trustedScriptSources} {$viteHttpSources}"),
    trim("style-src 'self' 'unsafe-inline' {$trustedStyleSources} {$viteHttpSources}"),
    trim("img-src 'self' data: blob: {$trustedImageSources} {$viteHttpSources}"),
    trim("font-src 'self' data: {$trustedFontSources} {$viteHttpSources}"),
    trim("connect-src 'self' {$trustedConnectSources} {$viteHttpSources} {$viteWebSocketSources}"),
    "media-src 'self'",
    "worker-src 'self' blob:",
    $isLocal ? null : 'upgrade-insecure-requests',
]));

return [
    'trusted_proxies' => array_values($trustedProxies),

    'trusted_hosts' => array_map(
        static fn (string $host): string => preg_quote($host, '/'),
        array_values($trustedHosts),
    ),

    'headers' => [
        'hsts' => env('SECURITY_HSTS_ENABLED', true),
        'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'permissions_policy' => env(
            'SECURITY_PERMISSIONS_POLICY',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), microphone=(), payment=(), usb=()',
        ),
    ],

    'csp' => [
        'enabled' => env('SECURITY_CSP_ENABLED', true),
        'report_only' => env('SECURITY_CSP_REPORT_ONLY', false),
        'policy' => env(
            'SECURITY_CSP_POLICY',
            $defaultCspPolicy,
        ),
    ],

    'login' => [
        'attempts_per_minute' => (int) env('LOGIN_ATTEMPTS_PER_MINUTE', 5),
        'ip_attempts_per_minute' => (int) env('LOGIN_IP_ATTEMPTS_PER_MINUTE', 20),
        'context_attempts_per_minute' => (int) env('LOGIN_CONTEXT_ATTEMPTS_PER_MINUTE', 15),
        'context_ip_attempts_per_minute' => (int) env('LOGIN_CONTEXT_IP_ATTEMPTS_PER_MINUTE', 30),
    ],
];
