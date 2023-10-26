<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\GraphqlException;
use App\Http\Controllers\Controller;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => ['required', 'min:2', 'exists:users'],
            'password' => 'required|min:5',
        ]);

        $user = User::where('username', $request->username)
            ->where('deleted_at', null)
            ->first();

        if ($user->active == false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username has been deactivate!.',
            ], 400);
        }

        $input = $request->only(['password', 'username']);


        if(!$token = auth()->attempt($input)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Login Credentials',
            ], 400);
        }

        //logs activity ### Muna please include activity log!


        return response()->json([
            'message' => 'Login Successful',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => $user,
                'permissions' => [],
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

}
