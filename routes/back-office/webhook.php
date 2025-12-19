<?php

use App\Http\Controllers\FacebookWebhookController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web']
], function () {
    Route::controller(FacebookWebhookController::class)->group(function () {
        Route::post('webhooks/facebook', 'handle')->name('webhooks.facebook');
    });
});