<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KeySecretMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('secret') != config('keysecret.app_secret') && $request->header('key') != config('keysecret.app_key')) {
            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'Access denied!',
                'data' => null,
            ], 403);
        }
        return $next($request);
    }
}
