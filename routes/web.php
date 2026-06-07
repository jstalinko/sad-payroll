<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SlipController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Karyawan CRUD
    Route::resource('karyawans', KaryawanController::class)->except(['create', 'edit', 'show']);

    // Slips management
    Route::get('slips', [SlipController::class, 'index'])->name('slips.index');
    Route::post('slips', [SlipController::class, 'store'])->name('slips.store');
    Route::delete('slips/{slip}', [SlipController::class, 'destroy'])->name('slips.destroy');
    
    Route::post('slips/mark-as-paid', [SlipController::class, 'markAsPaid'])->name('slips.markAsPaid');
    Route::get('slips/paid-list', [SlipController::class, 'paidList'])->name('slips.paidList');
    Route::get('slips/download-draft-pdf', [SlipController::class, 'downloadDraftPdf'])->name('slips.downloadDraftPdf');
    Route::get('slips/download-paid-pdf', [SlipController::class, 'downloadPaidPdf'])->name('slips.downloadPaidPdf');
    Route::get('slips/{slip}/download-pdf', [SlipController::class, 'downloadIndividualPdf'])->name('slips.downloadIndividualPdf');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
