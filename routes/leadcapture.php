<?php

use Illuminate\Support\Facades\Route;
use App\Modules\LeadCapture\Http\Controllers\LeadCaptureController;

// 🧩 LeadCapture Module Routes
Route::middleware(['web', 'auth'])
    ->prefix('leadcaptures')
    ->name('leadcaptures.')
    ->group(function () {

        // 🧱 Resource CRUD
        Route::resource('/', LeadCaptureController::class)
            ->parameters(['' => 'leadcapture']);

        // 🧩 Extra Actions (Grouped by Controller)
        Route::controller(LeadCaptureController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
            Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
            Route::post('{id}/restore', 'restore')->name('restore');
            Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
        });
    });