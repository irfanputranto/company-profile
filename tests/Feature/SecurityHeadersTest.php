<?php

use App\Providers\AppServiceProvider;
use Illuminate\Http\Middleware\TrustProxies;

it('mengizinkan sumber eksternal yang digunakan oleh antarmuka', function (): void {
    $policy = (string) config('security.csp.policy');

    expect($policy)
        ->toContain("script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://static.cloudflareinsights.com")
        ->toContain("style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com")
        ->toContain("font-src 'self' data: https://fonts.gstatic.com")
        ->toContain("connect-src 'self' https://cloudflareinsights.com");

    $this->get('/up')
        ->assertSuccessful()
        ->assertHeader('Content-Security-Policy', $policy);
});

it('mengenali https yang diteruskan oleh reverse proxy tepercaya', function (): void {
    TrustProxies::at(['172.20.0.10']);

    try {
        $this->withServerVariables([
            'REMOTE_ADDR' => '172.20.0.10',
        ])->withHeader('X-Forwarded-Proto', 'https')
            ->get('/up')
            ->assertSuccessful()
            ->assertHeader('Strict-Transport-Security');
    } finally {
        TrustProxies::at([]);
    }
});

it('menghasilkan endpoint aplikasi sebagai url same origin', function (): void {
    expect(route('dashboard', absolute: false))
        ->toBe('/dashboard')
        ->and(route('master.users.index', absolute: false))
        ->toBe('/master/users');
});

it('memaksa url https ketika app url menggunakan https', function (): void {
    config()->set('app.url', 'https://skeleton.example.test');
    (new AppServiceProvider(app()))->boot();

    expect(route('dashboard'))
        ->toStartWith('https://');
});

it('tidak mengirim header CSP ketika dinonaktifkan', function (): void {
    config()->set('security.csp.enabled', false);

    $this->get('/up')
        ->assertSuccessful()
        ->assertHeaderMissing('Content-Security-Policy');
});
