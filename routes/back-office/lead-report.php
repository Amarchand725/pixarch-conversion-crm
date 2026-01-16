<?php

use App\Http\Controllers\LeadReportController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth', 'permission.actions:lead_report']
], function () {
    // 🧱 Resource CRUD
    Route::resource('/', LeadReportController::class)
            ->parameters(['' => 'lead-report']);
});