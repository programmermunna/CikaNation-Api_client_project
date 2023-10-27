<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserIpAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->prefix('v1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/ip/create', [UserIpAddress::class, 'Create']);

});
