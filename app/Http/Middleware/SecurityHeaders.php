<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Attach OWASP-recommended security headers to every web response.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking — deny framing from other origins
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Stop leaking referrer info to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable old browser XSS filter (replaced by CSP, but still good hygiene)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Permissions Policy — restrict access to sensitive browser APIs
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()',
        );

        // Content-Security-Policy: allow only same-origin scripts, styles, fonts.
        // Adjust as needed (e.g. allow Google Fonts, your CDN, etc.)
        if (! app()->isLocal()) {
            $response->headers->set(
                'Content-Security-Policy',
                implode('; ', [
                    "default-src 'self'",
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval'", // unsafe-* needed for Vite/Inertia
                    "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com",
                    "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com https://fonts.googleapis.com",
                    "img-src 'self' data: blob:",
                    "connect-src 'self'",
                    "frame-ancestors 'self'",
                ]),
            );
        } else {
            // In local dev just set frame-ancestors to avoid breaking anything
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        }

        return $response;
    }
}
