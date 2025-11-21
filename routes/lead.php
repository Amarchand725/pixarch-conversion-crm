<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Lead\Http\Controllers\LeadController;

// 🧩 Lead Module Routes
Route::middleware(['web', 'auth'])
    ->prefix('leads')
    ->name('leads.')
    ->group(function () {

        // 🧱 Resource CRUD
        Route::resource('/', LeadController::class)
            ->parameters(['' => 'lead']);

        // 🧩 Extra Actions (Grouped by Controller)
        Route::controller(LeadController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
            Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
            Route::post('{id}/restore', 'restore')->name('restore');
            Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
            Route::post('update-status', 'updateStatus')->name('update-status');
        });
    });