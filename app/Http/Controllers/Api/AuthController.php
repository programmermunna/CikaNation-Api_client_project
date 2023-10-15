<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\GraphqlException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => [ 'required', 'exists:users'],
            'password' => ['required']
        ]);

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            $user = Auth::guard('sanctum')->user();

            $token = $user->createToken('authToken');

            return response()->json([
                'status' => 'success',
                'message' => 'Login Successful',
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => $user,
                    'token_type' => 'Bearer'
                ]
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Invalid Login Credentials'
        ], 400);
    }

}
