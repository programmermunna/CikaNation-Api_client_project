<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserIpController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['auth:api']], function () {

    /**
     * Admin module routes 
     */
    Route::name('admin.')->group(function () {
        Route::resource('user-ip', UserIpController::class);
        Route::resource('roles', RoleController::class);
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/download', [ActivityLogController::class, 'download'])->name('logs.download');
        Route::resource('user', UserController::class);
        Route::resource('permissions', PermissionController::class)->only('index', 'update');
    });



    /**
     * Service module routes
     */
    Route::name('service.')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('announcements', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('announcements', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });



    /**
     * Member Module routes
     */
    Route::name('member.')->group(function () {
    });
});
