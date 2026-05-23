<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Lead\Http\Controllers\LeadController;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:lead']
], function () {
    Route::controller(LeadController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{lead}/restore', 'restore')->name('restore');
        Route::delete('{lead}/force-delete', 'forceDelete')->name('forceDelete');
        Route::post('{lead}/update-status', 'updateStatus')->name('update-status');
        Route::get('import-form', 'importForm')->name('import-form');
        Route::post('import-data', 'import')->name('import-data');
        Route::get('load-more/{status}', 'loadMore')->name('load-more');

        Route::get('action/edit/{action}/{lead?}', 'actionEdit')->name('action.edit');
    });

    // 🧱 Resource CRUD
    Route::resource('/', LeadController::class)
            ->parameters(['' => 'lead']);
});