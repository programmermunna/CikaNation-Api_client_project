<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//auth


Route::group(['middleware' => ['auth:api']], function () {
    /**
     * Roles route goes here as resources route
     */
    Route::resource('roles',RoleController::class);
    Route::resource('user',UserController::class);
});
