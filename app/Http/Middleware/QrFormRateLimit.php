<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class QrFormRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'qr_form_rate_limit_' . $ip;

        // Check submissions in the last hour
        $submissions = Cache::get($key, []);

        // Remove submissions older than 1 hour
        $submissions = array_filter($submissions, function($timestamp) {
            return $timestamp > now()->subHour()->timestamp;
        });

        // Allow max 5 form submissions per hour per IP
        if (count($submissions) >= 5) {
            return redirect()->route('home')->with('error', 'Too many form submissions. Please try again later.');
        }

        $response = $next($request);

        // If this was a form submission (POST/PUT/PATCH), record it
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH']) && $response->isSuccessful()) {
            $submissions[] = now()->timestamp;
            Cache::put($key, $submissions, 3600); // Cache for 1 hour
        }

        return $response;
    }
}