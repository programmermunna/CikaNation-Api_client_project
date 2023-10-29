<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserIpController extends Controller
{

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data'   => UserIp::all(),
        ], 200);
    }

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
                        'message' => 'User Ip already exist',
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

            $UserIp = UserIp::create($payload);

            // Create Activity Log
            activity('create user ip')->causedBy(Auth::user()->id)
                ->performedOn($UserIp)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $UserIp->ip_address,
                    'activity' => 'Create user ip',
                ])
                ->log('Successfully');

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Created Successfully',
                'data' => $UserIp,
            ],200);

        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
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
                '*.min' => 'The number ip must be at least 0.',
                '*.max' => 'The number ip must not be greater than 255',
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

            $UserIp = UserIp::find($id);

            if (!$UserIp) {
                throw ValidationException::withMessages(["$UserIp Ip Not Found"]);
            }

            $ip1 = $request->number1;
            $ip2 = $request->number2;
            $ip3 = $request->number3;
            $ip4 = $request->number4;
            $ip_address = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;
            $checkIps = UserIp::select('ip_address')->where('ip_address', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')->whereNotIn('id', [$id])->pluck('ip_address')->toArray();
            if ($checkIps != []) {

                if (in_array($ip_address, $checkIps)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User Ip already exist',
                        'ip_whitelist' => $ip_address,
                    ], 400);
                }
            }

            // Update on Database
            $description = $request->description ?? $UserIp->description;
            $payload = [
                'ip_address' => $ip_address,
                'whitelisted' => $request->whitelisted,
                'description' => $description,
                'updated_by' => auth()->user()->id,
                'updated_at' => now(),
            ];

            $dataUpdate = [];
            if ($UserIp->ip_address != $ip_address) {
                $dataUpdate['ip_address'] = 'IP Address : ' . $UserIp->ip_address . ' -> ' . $ip_address;
            }
            if ($UserIp->whitelisted != $request->whitelisted) {
                $dataUpdate['whitelisted'] = 'Whitelisted : ' . ($UserIp->whitelisted == 1 ? 'True' : 'False') . ' -> ' . ($request->whitelisted == 1 ? 'True' : 'False');
            }
            if ($UserIp->description != $description) {
                $dataUpdate['description'] = 'Description : ' . $UserIp->description . ' -> ' . $description;
            }

            $dataLog = implode(', ', $dataUpdate) == null ? 'No Data Updated' : implode(', ', $dataUpdate);

            $UserIp->update($payload);

            // Create Activity Log
            activity('update User ip')->causedBy(Auth::user()->id)
                ->performedOn($UserIp)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $UserIp->ip_address,
                    'activity' => 'Updated User ip',
                ])
                ->log('Successfully Updated User ip, ' . $dataLog);

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Updated Successfully',
                'data' => $UserIp,
            ]);

        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }


    public function destroy($id)
    {
        try {
            $userIp = UserIp::find($id);

            if (!$userIp) {
                throw ValidationException::withMessages(["Ip With Id $id Not Found"]);
            }

            $userIp->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => now(),
            ]);

            activity('user_ip')->causedBy(Auth::user()->id)
                ->performedOn($userIp)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $userIp->ip_address,
                    'activity' => 'Deleted user ip',
                ])
                ->log('Successfully');

            $userIp->delete();

            return response()->json([
                'status' => 'successful',
                'message' => 'Ip Successfully Deleted',
                'data' => null,
            ]);
        } catch (\Exception$e) {
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

}
