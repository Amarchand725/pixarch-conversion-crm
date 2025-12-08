<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:user']
], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('change-password', 'changePassword')->name('change-password');
        Route::post('update-profile', 'updateProfile')->name('update-profile');
        Route::get('logout', 'logout')->name('logout');
    });
});