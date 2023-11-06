<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserIpResource;
use App\Models\UserIp;
use App\Trait\Authorizable;
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
            'data'   => UserIpResource::collection(UserIp::get()),
        ], 200);
    }

    use Authorizable;

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
                'whitelisted' => 'required|max:255|numeric',
                'description' => 'required|max:255',
            ],
            [
                'number1.required' => 'The number 1 IP is required',
                'number2.required' => 'The number 2 IP is required',
                'number3.required' => 'The number 3 IP is required',
                'number4.required' => 'The number 4 IP is required',
                'whitelisted.required' => 'The number whitelisted is required',
                'description.required' => 'The number whitelisted is required',
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

//        DB::beginTransaction();
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

//            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Updated Successfully',
                'data' => $UserIp,
            ],200);

        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }


    public function multi_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.item' => 'required|array',
            'items.*.item.number1' => 'required|integer',
            'items.*.item.number2' => 'required|integer',
            'items.*.item.number3' => 'required|integer',
            'items.*.item.number4' => 'required|integer',
            'items.*.item.whitelisted' => 'required|integer',
            'items.*.item.description' => 'required|string|max:255',
        ], [
            'items.required' => 'The items field is required.',
            'items.array' => 'The items must be an array.',
            'items.*.id.required' => 'The ID is required for item :itemIndex.',
            'items.*.id.integer' => 'The ID for item :itemIndex must be an integer.',
            'items.*.item.required' => 'The item for item :itemIndex is required.',
            'items.*.item.array' => 'The item for item :itemIndex must be an array.',
            'items.*.item.number1.required' => 'The number1 field for item :itemIndex is required.',
            'items.*.item.number1.integer' => 'The number1 field for item :itemIndex must be an integer.',
            'items.*.item.number2.required' => 'The number2 field for item :itemIndex is required.',
            'items.*.item.number2.integer' => 'The number2 field for item :itemIndex must be an integer.',
            'items.*.item.number3.required' => 'The number3 field for item :itemIndex is required.',
            'items.*.item.number3.integer' => 'The number3 field for item :itemIndex must be an integer.',
            'items.*.item.number4.required' => 'The number4 field for item :itemIndex is required.',
            'items.*.item.number4.integer' => 'The number4 field for item :itemIndex must be an integer.',
            'items.*.item.whitelisted.required' => 'The whitelisted field is required.',
            'items.*.item.whitelisted.integer' => 'The whitelisted field must be an integer.',
            'items.*.item.description.required' => 'The description field for item :itemIndex is required.',
            'items.*.item.description.string' => 'The description field for item :itemIndex must be a string.',
            'items.*.item.description.max' => 'The description field for item :itemIndex may not be greater than :max characters.',
        ]);

        $validator->setAttributeNames([
            'items.*.id' => 'ID',
            'items.*.item.number1' => 'Number 1',
            'items.*.item.number2' => 'Number 2',
            'items.*.item.number3' => 'Number 3',
            'items.*.item.number4' => 'Number 4',
            'items.*.item.whitelisted' => 'Whitelisted',
            'items.*.item.description' => 'Description',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            foreach ($errors as $key => $messages) {
                foreach ($messages as $message) {
                    $customMessages[] = str_replace(':itemIndex', $key, $message);
                }
            }
            return response()->json(['errors' => $customMessages], 422);
        }

        try {
//            DB::beginTransaction();
            $userIdData = [];
            $items = $request->input('items');
            foreach($items as $item){
                $id = $item['id'];

                if ($item['item']['number3'] === null && $item['item']['number4'] !== null) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Format IP Invalid!.',
                    ], 400);
                }

                $UserIp = UserIp::find($id);

                if (!$UserIp) {
                    throw ValidationException::withMessages(["$UserIp Ip Not Found"]);
                }

                $ip1 = $item['item']['number1'];
                $ip2 = $item['item']['number2'];
                $ip3 = $item['item']['number3'];
                $ip4 = $item['item']['number4'];
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
                $description = $item['item']['description'] ?? $UserIp->description;
                $payload = [
                    'ip_address' => $ip_address,
                    'whitelisted' => $item['item']['whitelisted'],
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

                $userIdData[] = $UserIp;
            }

//            DB::commit();
            return response()->json([
                'status' => 'successful',
                'message' => 'Users Ip Updated Successfully',
                'data' =>  $userIdData,
            ],200);

        } catch (\Exception$e) {
            Log::error($e);
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    public function destroy($ids)
    {

        try {
            $ids = explode(',',$ids);

            foreach ($ids as $id_check){
                $userIp = UserIp::find($id_check);
                if (!$userIp) {
                    throw ValidationException::withMessages(["Ip With Id $id_check Not Found, Please Send Valid data"]);
            }}

            foreach ($ids as $id){
                $userIp = UserIp::find($id);
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
            }



            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Successfully Deleted',
                'data' => null,
            ]);
        } catch (\Exception$e) {
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

}
