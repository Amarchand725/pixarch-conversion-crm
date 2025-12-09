<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ActivityLog\Http\Controllers\ActivityLogController;

// 🧩 ActivityLog Module Routes
Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:activity_log']
], function () {
    // 🧱 Resource CRUD
    Route::resource('/', ActivityLogController::class)
            ->parameters(['' => 'activity-log']);
});