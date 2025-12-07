<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// 🧩 Faq Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::post('mark-all-read', 'markAllRead');
        Route::post('{notification}/read', 'markRead');
    });

    // 🧱 Resource CRUD
    Route::resource('/', NotificationController::class)
            ->parameters(['' => 'notification']);
});