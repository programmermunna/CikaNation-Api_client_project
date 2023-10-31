<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use App\Trait\Authorizable;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = UserPermission::whereNull('parent_id')->with('children')->get();

        return response()->json([
            'data' => $data
        ],200);
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
