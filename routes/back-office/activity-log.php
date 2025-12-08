<?php

use Illuminate\Support\Facades\Route;
use App\Modules\ActivityLog\Http\Controllers\ActivityLogController;

// 🧩 ActivityLog Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    // 🧱 Resource CRUD
    Route::resource('/', ActivityLogController::class)
            ->parameters(['' => 'activity-log']);
});