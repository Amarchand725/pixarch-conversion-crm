<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// 🧩 Faq Module Routes
Route::group([
    'middleware' => ['web', 'auth']
], function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::get('mark-all-read', 'markAllRead')->name('mark-all-read');
    });

    // 🧱 Resource CRUD
    Route::resource('/', NotificationController::class)
            ->parameters(['' => 'notification']);
});