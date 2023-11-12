<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\CashflowController;
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
        Route::apiResource('user-ip', UserIpController::class);
        Route::put('/user-ips', [UserIpController::class, 'multiUpdate'])
            ->name('user-ip.multi_update');
        Route::apiResource('roles', RoleController::class);
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/download', [ActivityLogController::class, 'download'])->name('logs.download');
        Route::apiResource('user', UserController::class);
        Route::apiResource('permissions', PermissionController::class)->only('index', 'update');
    });



    /**
     * Service module routes
     */
    Route::name('service.')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('announcements', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('announcements', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::apiResource('cashflows', CashflowController::class);
        Route::delete('cashflows-delete-many', [CashflowController::class,'deleteMany'])->name('cashflows.delete_many');
    });



    /**
     * Member Module routes
     */
    Route::name('member.')->group(function () {
    });
});
