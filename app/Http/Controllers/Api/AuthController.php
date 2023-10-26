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
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|min:2',
                'password' => 'required|min:5',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $user = User::select([
            'id',
            'username',
            'active',
            'password',
            'suspended_at'
        ])
            ->where('username', $request->username)
            ->where('deleted_at', null)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username not found!.',
            ], 400);
        }
        if ($user->active == false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username has been deactivate!.',
            ], 400);
        }

        $checkPassword = Hash::check($request->password, $user->password);
        if (!$checkPassword) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your password is wrong!',
            ], 400);
        }

        // WEB SOCKET START
        // AuthUserLoginEvent::dispatch($user->id);
        // WEB SOCKET FINISH

        $input = $request->only(['password', 'username']);


        if(!$token = auth()->attempt($input)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Login Credentials',
            ], 400);
        }


        return response()->json([
            'message' => 'Login Successful',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => $user
            ],
        ], 200);

        // $user = auth()->user();

        // $user->update([
        //     'timezone' => $request->timezone ?? null,
        //     'last_login_at' => Carbon::now(),
        //     'last_login_ip' => $request->ip ?? $request->getClientIp() ?? "0.0.0.0",
        //     'remember_token' => $token,
        // ]);

        // $user->has_pin = $user->verification_pin !== null;
        // if ($token) {
        //     event(new UserLoggedIn(auth()->user()));
        //     $permissions = $this->permissions(auth()->user()->id);
        //     return response()->json([
        //         'message' => 'Login Successful',
        //         'status' => 'success',
        //         'data' => [
        //             'token' => $token,
        //             'user' => $user,
        //             'retries' => RateLimiter::retriesLeft('user-pin-verification:' . $user->id, 5),
        //             'permissions' => $permissions,
        //         ],
        //     ]);
        // }
    }

}
