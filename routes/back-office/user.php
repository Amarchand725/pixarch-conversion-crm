<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:user']
], function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', UserController::class)
            ->parameters(['' => 'user']);
});