<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetImportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\SpmDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/peta', [MapController::class, 'index'])->name('peta');
Route::get('/peta/data', [MapController::class, 'data'])->name('peta.data');
Route::get('/provinsi/{province}', [MapController::class, 'show'])->name('provinsi.detail');

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

    Route::get('/import', [DatasetImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [DatasetImportController::class, 'preview'])->name('import.preview');
    Route::post('/import', [DatasetImportController::class, 'store'])->name('import.store');
    Route::patch('/import/{importHistory}/publish', [DatasetImportController::class, 'publish'])->name('import.publish');
    Route::patch('/import/{importHistory}/draft', [DatasetImportController::class, 'draft'])->name('import.draft');
    Route::delete('/import/{importHistory}', [DatasetImportController::class, 'destroy'])->name('import.destroy');
    Route::post('/reconciliation/preview', [ReconciliationController::class, 'preview'])->name('reconciliation.preview');

    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');
});
