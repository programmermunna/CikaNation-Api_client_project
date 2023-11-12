<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use App\Trait\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = UserPermission::whereNull('parent_id')->get();

        $dataset = [];

        $data->each(function($module) use(&$dataset){
            foreach($module->children as $child){

                if($child->children){
                   foreach($child->children as $value){
                    $dataset[$module->name][$child->name][] = [
                        'id' => $value->id,
                        'parent_id' => $child->id,
                        'module_id' => $module->id,
                        'name'      => $value->display_name ?? $value->name,
                        'created_at' => $value->created_at->format('d-M-Y H:i:s')
                    ];
                   }
                }

            }
        });


        return response()->json([
            'data' => $dataset
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      => [
                'required',
                'string',
                Rule::unique('permissions','display_name')->ignore($id),
            ],
            'parent_id' => 'nullable|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($id);

        DB::beginTransaction();
        try {
            $permission->update([
                'display_name' => $request->name,
                'parent_id'    => $request->parent_id ?? $permission->parent_id,
            ]);


            activity("Permission updated")
                ->causedBy(auth()->user())
                ->performedOn($permission)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role created successfully",
                    'target' => "$permission->name",
                ])
                ->log(":causer.name created Role $permission->display_name.");

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully permission updated',
                'data'    => $permission
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'success' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
