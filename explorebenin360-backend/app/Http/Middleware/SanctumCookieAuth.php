<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanctumCookieAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('eb360_token')) {
            $token = $request->cookie('eb360_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }
        return $next($request);
    }
}
