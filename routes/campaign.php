<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Campaign\Http\Controllers\CampaignController;

// 🧩 Campaign Module Routes
Route::middleware(['web', 'auth'])
    ->prefix('campaigns')
    ->name('campaigns.')
    ->group(function () {

        // 🧱 Resource CRUD
        Route::resource('/', CampaignController::class)
            ->parameters(['' => 'campaign']);

        // 🧩 Extra Actions (Grouped by Controller)
        Route::controller(CampaignController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
            Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
            Route::post('{id}/restore', 'restore')->name('restore');
            Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
        });
    });