<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data'   => User::with('roles')->get(),
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
        $this->validate($request, [

            'username' => 'required|string|unique:users|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required',
        ]);

        $roles = $request->roles ?? [];
        $roles_ids = Role::select('id')->whereHas('permissions')->get()->pluck('id')->toArray();
        foreach ($roles as $role_id) {
            if (!in_array($role_id, $roles_ids)) {
                throw ValidationException::withMessages(["role with id $role_id does not exist!"]);
            }
        }


        $input = $request->only([
            'name',
            'username',
            'email',
        ]);
        $input['created_by'] = auth()->user()->id ?? 0;
        $input['password'] = Hash::make($request->password);

        try {
            DB::beginTransaction();
            $user = User::create($input);
            $user->roles()->sync($roles);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }

        DB::commit();
        # log activity
        activity('create_user')->causedBy(Auth::user()->id ?? 1)
            ->performedOn($user)
            ->withProperties([
                'ip' => Auth::user()->last_login_ip ?? $request->ip(),
                'target' => $request->username,
                'activity' => 'Created user successfully',
            ])
            ->log('Created user successfully');
        return response()->json([
            'status' => 'successful',
            'message' => 'User Created Sucessfully',
            'data' => $user->load('roles'),
        ]);
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
     * Update user.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'roles' => 'required',
        ]);

        if (count($request->roles) > 0) {
            $roles = Role::whereIn('id', $request->roles)->get()->pluck('id')->toArray();
            $unMatchedRolesId = array_diff($request->roles, $roles);

            if (count($unMatchedRolesId) > 0) {
                throw ValidationException::withMessages(["There is No Permission with Id(s) [" . implode(', ', $unMatchedRolesId)]);
            }
        }

        $user = User::find($id);
        if ($user) {
            $input = $request->only([
                'name',
                'username',
                'email',
            ]);
            $input['updated_by'] = Auth::user()->id;
            $input['roles'] = $request->roles ?? $user->roles->toArray();

            $user->update($input);
            $user->roles()->sync($input['roles'] ?? []);

            $user->load('roles');
            # log activity
            activity('update_user')->causedBy(Auth::user()->id)
                ->performedOn($user)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $request->name,
                    'activity' => 'Update user successfully',
                ])
                ->log('Update user successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'User Updated Successfully',
                'data' => $user,
            ]);
        }

        throw ValidationException::withMessages(['User Not Found!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids)
    {
        try {
            $ids = explode(',',$ids);

            foreach ($ids as $id_check){
                $user = User::find($id_check);
                if (!$user) {
                    throw ValidationException::withMessages(["With Id $id_check Not Found, Please Send Valid data"]);
            }}

            foreach ($ids as $id){
                $user = User::find($id);
                $user->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => now(),
                ]);

                activity('user')->causedBy(Auth::user()->id)
                    ->performedOn($user)
                    ->withProperties([
                        'user' => Auth::user()->last_login_ip,
                        'target' => $user->ip_address,
                        'activity' => 'Deleted user',
                    ])
                    ->log('Successfully');

                $user->delete();
            }

            return response()->json([
                'status' => 'successful',
                'message' => 'User Successfully Deleted',
                'data' => null,
            ]);
        } catch (\Exception$e) {
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }
}
