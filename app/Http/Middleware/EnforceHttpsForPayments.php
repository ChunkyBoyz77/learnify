<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PaymentSecurityService;
use Illuminate\Support\Facades\Log;

class EnforceHttpsForPayments
{
    protected PaymentSecurityService $securityService;

    public function __construct(PaymentSecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     * Enforces HTTPS for payment-related routes and logs non-secure connections.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request is secure (HTTPS)
        $isSecure = $request->secure() || 
                   $request->header('X-Forwarded-Proto') === 'https' ||
                   $request->header('X-Forwarded-Ssl') === 'on';

        // If not secure, log the security event
        if (!$isSecure) {
            $this->securityService->logSecurityEvent(
                'non_secure_connection',
                'critical',
                "Payment-related request attempted over non-HTTPS connection: {$request->fullUrl()}",
                null,
                [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'protocol' => $request->getScheme(),
                    'route' => $request->route()?->getName(),
                ]
            );

            // In production, redirect to HTTPS
            if (app()->environment('production')) {
                $url = $request->fullUrl();
                $url = str_replace('http://', 'https://', $url);
                return redirect($url, 301);
            }
        }

        return $next($request);
    }
}
