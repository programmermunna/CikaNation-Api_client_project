<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        if (!$user->active) {
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

        $user->update([
            'timezone' => $request->timezone ?? null,
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $request->ip ?? $request->getClientIp() ?? "0.0.0.0",
            'remember_token' => $token,
        ]);

        //logs activity ### Muna please include activity log!
        activity('User Login')->causedBy(Auth::user()->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => Auth::user()->last_login_ip,
                'target' => $request->username,
                'activity' => 'User Login successfully',
            ])
            ->log('User Login successfully');


        return response()->json([
            'message' => 'Login Successful',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => $user,
                'permissions' => $this->permissions($user->id),
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    //@todo muna please add a unit test to cover this. !IMPORTANT
    protected function permissions($userId): array|\Throwable|\Exception
    {
        try {
            $permissionAgents = User::with('permissionUser')->select('id')->find($userId)->toArray()['permission_user'][0]['role'][0]['permissions'];
            $permissions = \Spatie\Permission\Models\Permission::get()->toArray();

            $newPermissions = [];
            foreach ($permissions as $key => $admin) {
                $permissionAgent = collect($permissionAgents)->where('id', $admin['id'])->all();
                $permission = array_values($permissionAgent);
                if ($permission != []) {
                    $newPermissions[] = [
                        'id' => $permission[0]['id'],
                        'name' => $permission[0]['name'],
                        'description' => $permission[0]['description'],
                        'group_by' => $permission[0]['group_by'],
                        'modul_name' => $permission[0]['modul_name'],
                        'permission_access' => true,
                    ];
                } else {
                    $newPermissions[] = [
                        'id' => $admin['id'],
                        'name' => $admin['name'],
                        'description' => $admin['description'],
                        'group_by' => $admin['group_by'],
                        'modul_name' => $admin['modul_name'],
                        'permission_access' => false,
                    ];
                }
            }

            $admin = collect($newPermissions)->where('modul_name', 'admin')->groupBy('group_by')->all();

            $data = [
                'Agent' => [$admin],
            ];

            return $data;
        } catch (\Throwable $exception) {
            logger('permission pulling Error');

            Log::error($exception);
            return [];
        }
    }
}
