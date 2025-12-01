<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Lead\Http\Controllers\LeadController;

Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(LeadController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
        Route::post('update-status', 'updateStatus')->name('update-status');
        Route::post('import-data', 'import')->name('import');
    });

    // 🧱 Resource CRUD
    Route::resource('/', LeadController::class)
            ->parameters(['' => 'lead']);
});