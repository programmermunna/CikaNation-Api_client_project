<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthenticationMiddleware
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
        try {
            $token = $request->header('Authorization');
            $rememberToken = 'Bearer ' . Auth::user()->remember_token;

            if ($token == $rememberToken) {
                return $next($request);
            }

            Auth::logout();

            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized, Please login again.',
                'data' => [],
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error.',
                'data' => $th->getMessage(),
            ], 500);
        }
    }
}
