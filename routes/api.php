<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//auth


Route::group(['middleware' => ['auth:api', 'authLogin']], function () {
});
