<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserIP\UserIpRequest;
use App\Http\Resources\Api\UserIpResource;
use App\Http\Resources\UserIpResourceCollection;
use App\Models\UserIp;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserIpController extends Controller
{
    use Authorizable;

    public function index(): AnonymousResourceCollection
    {
        $UserIps = UserIp::paginate(AppConstant::PAGINATION);

        return UserIpResource::collection($UserIps);
    }


    /**
     * @throws ValidationException
     */
    public function store(UserIpRequest $request): JsonResponse
    {
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
            $checkIps = UserIp::select('ip_address')
                ->where('ip_address', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')
                ->pluck('ip_address')
                ->toArray();

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
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(UserIpRequest $request, $id): JsonResponse
    {

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

            $checkIps = UserIp::select('ip_address')
                ->where('ip_address', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')
                ->whereNotIn('id', [$id])
                ->pluck('ip_address')
                ->toArray();

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
            ],200);

        } catch (\Exception$e) {
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }


    /**
     * @throws ValidationException
     */
    public function multiUpdate(UserIpRequest $request): JsonResponse
    {

        try {
            DB::beginTransaction();
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

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'Users Ip Updated Successfully',
                'data' =>  $userIdData,
            ],200);

        } catch (\Exception$e) {
            DB::rollBack();
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
