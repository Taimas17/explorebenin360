<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottleResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        if ($response->getStatusCode() === 429) {
            return response()->json([
                'message' => 'Too many requests. Please slow down.',
                'retry_after' => $response->headers->get('Retry-After'),
            ], 429, $response->headers->all());
        }
        
        return $response;
    }
}