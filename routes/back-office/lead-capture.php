<?php

use Illuminate\Support\Facades\Route;
use App\Modules\LeadCapture\Http\Controllers\LeadCaptureController;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:lead_capture']
], function () {
    Route::controller(LeadCaptureController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', LeadCaptureController::class)
            ->parameters(['' => 'lead-capture']);
});