<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:user']
], function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{user}/restore', 'restore')->name('restore');
        Route::delete('{user}/force-delete', 'forceDelete')->name('forceDelete');

        Route::get('action/edit/{user?}', 'editPassword')->name('action.edit');
        Route::put('action/update-password/{user?}', 'changePassword')->name('action.update-password');
    });

    // 🧱 Resource CRUD
    Route::resource('/', UserController::class)
            ->parameters(['' => 'user']);
});