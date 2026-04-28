<?php

use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\LeadCapturePublicController;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    abort(404);
});

Route::get('/', function () {
    return redirect()->route('back-office.auth.dashboard');
});

Route::controller(LeadCapturePublicController::class)->group(function () {
    Route::get('/lead-capture/form/{uuid}', 'show')->name('lead-capture.public');
    Route::post('/lead-capture/form/{uuid}', action: 'store')->name('lead-capture.store');
});

Route::controller(DeveloperController::class)->group(function () {
    // Route::get('/exported-contacts', 'exportedContacts')->name('exported-contacts');
    Route::get('/import-opportunities', 'importOpportunities')->name('import-opportunities');
    Route::get('/get-opportunities-assignee', 'getOpportunitiesAssignee')->name('get-opportunities-assignee');
});

Route::impersonate();

require __DIR__.'/auth.php';
