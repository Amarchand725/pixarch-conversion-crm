<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Meeting\Http\Controllers\MeetingController;

// 🧩 Meeting Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(MeetingController::class)->group(function () {
        Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
        Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
        Route::post('{id}/restore', 'restore')->name('restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
        Route::post('{meeting}/update-status', 'updateStatus')->name('update-status');

        Route::get('action/edit/{action}/{meeting?}', 'actionEdit')->name('action.edit');
        Route::get('calendar/events', 'calendarEvents')->name('calendar.events');
    });

    // 🧱 Resource CRUD
    Route::resource('/', MeetingController::class)
            ->parameters(['' => 'meeting']);
});