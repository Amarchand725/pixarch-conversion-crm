<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadCapturePublicController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('back-office.auth.dashboard');
});

Route::get('/lead-capture/form/{uuid}', [LeadCapturePublicController::class, 'show'])
    ->name('lead-capture.public');
Route::post('/lead-capture/form/{uuid}', [LeadCapturePublicController::class, 'store'])
    ->name('lead-capture.store');

// Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::impersonate();

require __DIR__.'/auth.php';
