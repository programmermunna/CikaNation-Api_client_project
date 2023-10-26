<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data'   => Role::all(),
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|unique:roles,name',
            'permissions'   => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();

        try {

            $role = Role::create([
                'name' => $request->name,
            ]);

            $role->permissions()->sync(@$request->permissions ?? []);

            activity("Role created")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role created successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name created Role $role->name.");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Role Created!!',
                'data' => $role,
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        $request->validate([
            'name'          => [
                'required',
                'string',
                Rule::unique('roles')->ignore($id)
            ],
            'permissions'   => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();

        try {

            $role = Role::find($id);
            
            if(!$role){
                throw new \Exception('No role found',404);
            }

            $role->update([
                'name' => $request->name,
            ]);

            $role->permissions()->detach();

            $role->permissions()->sync(@$request->permissions ?? []);

            activity("Role updated")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role updated successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name created Role $role->name.");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Role Updated!!',
                'data' => $role,
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {


            $role = Role::findOrFail($id);

            $role->permissions()->detach();


            activity("Role deleted")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role deleted successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name deleted Role $role->name.");



            Role::where('id', $id)->delete();


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Role Updated!!',
                'data' => $role,
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
}