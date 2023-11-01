<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

//auth


Route::group(['middleware' => ['auth:api']], function () {
    /**
     * Roles route goes here as resources route
     */
    Route::resource('roles',RoleController::class);
    Route::resource('permissions',PermissionController::class)->only('index','update');
});
