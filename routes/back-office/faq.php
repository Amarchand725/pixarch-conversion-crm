<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Faq\Http\Controllers\FaqController;

// 🧩 Faq Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(FaqController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
    });

    // 🧱 Resource CRUD
    Route::resource('/', FaqController::class)
            ->parameters(['' => 'faq']);
});