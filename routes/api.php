<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserIpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//auth


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::resource('ip',UserIpController::class);
    Route::resource('roles',RoleController::class);
});
