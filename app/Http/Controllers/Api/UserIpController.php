<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserIpResource;
use App\Models\UserIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserIpController extends Controller
{
    public function store(Request $request)
    {

        // Validation
        $validator = Validator::make(
            $request->all(),
            [
                'number1' => 'required|min:1|max:255|numeric',
                'number2' => 'required|min:0|max:255|numeric',
                'number3' => 'required|min:0|max:255|numeric',
                'number4' => 'required|min:0|max:255|numeric',
                'description' => 'required',
            ],
            [
                'number1.required' => 'The number 1 IP is required',
                'number2.required' => 'The number 2 IP is required',
                'number3.required' => 'The number 3 IP is required',
                'number4.required' => 'The number 4 IP is required',
                '*.min' => 'The number must be at least 0.',
                '*.max' => 'The number must not be greater than 255',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        if ($request->number3 === null && $request->number4 !== null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format IP Invalid!.',
            ], 400);
        }


        DB::beginTransaction();
        try {
            $ip1 = $request->number1;
            $ip2 = $request->number2;
            $ip3 = $request->number3;
            $ip4 = $request->number4;
            $ip_address = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;
            $checkIps = UserIp::select('ip_address')->where('ip_address', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')->pluck('ip_address')->toArray();
            if ($checkIps != []) {
                if (in_array($ip_address, $checkIps)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Admin Ip already exist',
                        'ip_whitelist' => $ip_address,
                    ], 400);
                }
            }

            // Insert to Database
            $payload = [
                'ip_address' => $ip_address,
                'whitelisted' => 1,
                'description' => $request->description,
                'created_by' => Auth::user()->id,
                'created_at' => now(),
            ];

            $model = UserIp::create($payload);

            // Create Activity Log
            activity('create user ip')->causedBy(Auth::user()->id)
                ->performedOn($model)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $model->ip_address,
                    'activity' => 'Create user ip',
                ])
                ->log('Successfully');

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Created Successfully',
                'data' => new UserIpResource($model),
            ]);

        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }
}