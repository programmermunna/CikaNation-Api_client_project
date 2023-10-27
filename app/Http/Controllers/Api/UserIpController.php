<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserIps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserIpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userIps = UserIps::with('user')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $userIps,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

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
            $checkIps = UserIps::select('ip_address')->where('ip_address', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')->pluck('ip_address')->toArray();
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
                'created_by' => auth()->user()->id,
                'created_at' => now(),
            ];

            $model = UserIps::create($payload);

            // Create Activity Log
            activity('create User ip')->causedBy(Auth::user()->id)
                ->performedOn($model)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $model->ip_address,
                    'activity' => 'Create User ip',
                ])
                ->log('Successfully');

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Created Successfully',
                'data' => $model,
            ]);
        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
