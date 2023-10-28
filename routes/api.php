<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//auth


Route::group(['middleware' => ['auth:api']], function () {
    /**
     * Roles route goes here as resources route
     */
    Route::resource('roles',RoleController::class);
    Route::get('announcements',[AnnouncementController::class,'index'])->name('announcements.index');
    Route::post('announcements',[AnnouncementController::class,'store'])->name('announcements.store');
    Route::put('announcements',[AnnouncementController::class,'update'])->name('announcements.update');
    Route::delete('announcements',[AnnouncementController::class,'destroy'])->name('announcements.destroy');
});