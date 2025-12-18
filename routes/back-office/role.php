<?php

use App\Modules\Role\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:role']
], function () {
    Route::controller(RoleController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{role}/restore', 'restore')->name('restore');
        Route::delete('{role}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', RoleController::class)
            ->parameters(['' => 'role']);
});