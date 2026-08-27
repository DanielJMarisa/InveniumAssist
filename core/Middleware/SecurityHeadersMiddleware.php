<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;

final class SecurityHeadersMiddleware extends Middleware
{
    /**
     * Handle incoming request and apply security headers.
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed {
        /*
        |--------------------------------------------------------------------------
        | Prevent MIME-type sniffing
        |--------------------------------------------------------------------------
        */

        header(
            'X-Content-Type-Options: nosniff'
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent clickjacking
        |--------------------------------------------------------------------------
        */

        header(
            'X-Frame-Options: DENY'
        );

        /*
        |--------------------------------------------------------------------------
        | Referrer Policy
        |--------------------------------------------------------------------------
        */

        header(
            'Referrer-Policy: strict-origin-when-cross-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | Browser Permissions Policy
        |--------------------------------------------------------------------------
        */

        header(
            'Permissions-Policy: '
            . 'camera=(), '
            . 'microphone=(), '
            . 'geolocation=(), '
            . 'payment=()'
        );

        /*
        |--------------------------------------------------------------------------
        | Legacy XSS Protection
        |--------------------------------------------------------------------------
        |
        | Modern browsers generally ignore this header, but explicitly
        | disabling the legacy filter avoids inconsistent behaviour.
        |
        */

        header(
            'X-XSS-Protection: 0'
        );

        /*
        |--------------------------------------------------------------------------
        | Cross-Origin Resource Policy
        |--------------------------------------------------------------------------
        */

        header(
            'Cross-Origin-Resource-Policy: same-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | HSTS
        |--------------------------------------------------------------------------
        |
        | Only send HSTS when the current request is HTTPS.
        | This prevents local HTTP development from being locked into HTTPS.
        |
        */

        if ($this->isHttps()) {

            header(
                'Strict-Transport-Security: '
                . 'max-age=31536000; '
                . 'includeSubDomains'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Continue Request Pipeline
        |--------------------------------------------------------------------------
        */

        return $this->next(
            $request,
            $next
        );
    }

    /**
     * Determine whether the current request uses HTTPS.
     */
    private function isHttps(): bool
    {
        if (
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            &&
            strtolower(
                (string) $_SERVER['HTTP_X_FORWARDED_PROTO']
            ) === 'https'
        ) {
            return true;
        }

        return !empty($_SERVER['HTTPS'])
            && strtolower(
                (string) $_SERVER['HTTPS']
            ) !== 'off';
    }
}