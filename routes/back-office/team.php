<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Team\Http\Controllers\TeamController;

// 🧩 Team Module Routes
Route::middleware(['web', 'auth'])
    ->prefix('teams')
    ->name('teams.')
    ->group(function () {

        // 🧱 Resource CRUD
        Route::resource('/', TeamController::class)
            ->parameters(['' => 'team']);

        // 🧩 Extra Actions (Grouped by Controller)
        Route::controller(TeamController::class)->group(function () {
            Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
            Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
            Route::post('{id}/restore', 'restore')->name('restore');
            Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
        });
    });