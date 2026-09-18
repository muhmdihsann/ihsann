<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\SpmDataController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ExportController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peta', [MapController::class, 'index'])->name('peta');
Route::get('/provinsi/{id}', [MapController::class, 'show'])->name('provinsi.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/spm', [SpmDataController::class, 'index'])->name('spm.index');
    Route::get('/spm/{id}/edit', [SpmDataController::class, 'edit'])->name('spm.edit');
    Route::put('/spm/{id}', [SpmDataController::class, 'update'])->name('spm.update');
    Route::delete('/spm/{id}', [SpmDataController::class, 'destroy'])->name('spm.destroy');

    Route::get('/provinces', [ProvinceController::class, 'index'])->name('provinces.index');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/store', [ImportController::class, 'store'])->name('import.store');
    Route::post('/reconciliation/preview', [ReconciliationController::class, 'preview'])->name('reconciliation.preview');

    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');
});
