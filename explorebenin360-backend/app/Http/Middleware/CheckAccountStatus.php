<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && !$request->user()->isActive()) {
            $message = $request->user()->isBanned()
                ? 'Your account has been banned.'
                : 'Your account is suspended.';

            Auth::logout();
            return response()->json(['message' => $message], 403);
        }
        return $next($request);
    }
}