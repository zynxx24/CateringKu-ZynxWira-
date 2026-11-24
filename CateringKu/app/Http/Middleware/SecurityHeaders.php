<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Comprehensive security headers to harden the application against
     * XSS, clickjacking, MIME sniffing, data injection, and other common attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $isDev = config('app.env') === 'local';

        // ── Anti-Sniffing ──────────────────────────────────
        // Prevent MIME type sniffing (stop IE/Chrome from guessing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Anti-Clickjacking ──────────────────────────────
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ── XSS Auditor ────────────────────────────────────
        // The legacy auditor is itself exploitable; modern guidance is to disable it and rely on CSP.
        $response->headers->set('X-XSS-Protection', '0');

        // ── Referrer Policy ─────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Permissions / Feature Policy ─────────────────────
        $response->headers->set('Permissions-Policy', implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=(self)',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
        ]));

        // ── Cross-Origin Policies ───────────────────────────
        // Keep dev relaxed to avoid noisy browser warnings on non-HTTPS/local binds.
        $response->headers->set('Cross-Origin-Resource-Policy', $isDev ? 'cross-origin' : 'same-origin');
        if (! $isDev) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request, $isDev));

        // ── Cache Control (for authenticated/sensitive pages) ─
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        // ── HTTPS Strict Transport Security ─────────────────
        if ($request->isSecure() || config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        // ── Remove Server Identification Headers ────────────
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * In local development the Vite dev server (HMR over HTTP + WebSocket) must be allowed;
     * production gets a strict policy without 'unsafe-eval' or extra origins.
     */
    private function contentSecurityPolicy(Request $request, bool $isDev): string
    {
        $devScript = '';
        $devAssets = '';
        $devConnect = '';

        if ($isDev) {
            $port = (int) config('cateringku.vite_dev_port');
            $hosts = array_values(array_unique(array_filter([
                '127.0.0.1',
                'localhost',
                parse_url((string) config('app.url'), PHP_URL_HOST),
                $request->getHost(),
            ])));
            $http = array_map(fn (string $host) => "http://{$host}:{$port}", $hosts);
            $ws = array_map(fn (string $host) => "ws://{$host}:{$port}", $hosts);

            $devAssets = ' '.implode(' ', $http);
            $devScript = " 'unsafe-eval'{$devAssets}";
            $devConnect = ' '.implode(' ', [...$http, ...$ws]);
        }

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'{$devScript}",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com{$devAssets}",
            "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:{$devAssets}",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' wss: https:{$devConnect}",
            "media-src 'self'",
            "object-src 'none'",
            "child-src 'self'",
            "frame-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "manifest-src 'self'",
            "worker-src 'self' blob:",
        ]);
    }
}
