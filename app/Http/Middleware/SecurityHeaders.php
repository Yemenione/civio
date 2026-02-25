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

        $response->headers->set('X-Content-Type-Options',   'nosniff');
        $response->headers->set('X-Frame-Options',          'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection',         '1; mode=block');
        $response->headers->set('Referrer-Policy',          'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy',       'camera=(), microphone=(), geolocation=()');
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        // Only enforce strict CSP in production — Vite HMR on localhost breaks CSP validation
        if (! app()->environment('local')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://accounts.google.com; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                "font-src 'self' https://fonts.gstatic.com; " .
                "img-src 'self' data: https:; " .
                "connect-src 'self' https://api.stripe.com https://api.mistral.ai; " .
                "frame-src https://js.stripe.com https://hooks.stripe.com https://accounts.google.com;"
            );
        }

        // Remove fingerprinting headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
