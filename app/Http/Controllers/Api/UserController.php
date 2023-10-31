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
            'roles' => ['required'],
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
