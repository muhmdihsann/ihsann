<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/peta', [App\Http\Controllers\MapController::class, 'index'])->name('peta');
Route::get('/provinsi/{id}', [App\Http\Controllers\MapController::class, 'show'])->name('provinsi.detail');

// Route Autentikasi (Hanya bisa diakses jika belum login / guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

// Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Area Operator (Hanya bisa diakses jika sudah login)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route Manajemen Data SPM
    Route::get('/spm', [App\Http\Controllers\SpmDataController::class, 'index'])->name('spm.index');

    // Route Manajemen Data SPM
    Route::get('/spm', [App\Http\Controllers\SpmDataController::class, 'index'])->name('spm.index');
    Route::get('/spm/{id}/edit', [App\Http\Controllers\SpmDataController::class, 'edit'])->name('spm.edit');
    Route::put('/spm/{id}', [App\Http\Controllers\SpmDataController::class, 'update'])->name('spm.update');
    Route::delete('/spm/{id}', [App\Http\Controllers\SpmDataController::class, 'destroy'])->name('spm.destroy');
    // Nanti route untuk CRUD, Import Excel, dll kita taruh di dalam group ini.
    Route::get('/provinces', [App\Http\Controllers\ProvinceController::class, 'index'])->name('provinces.index');

    // Route untuk Import Excel
    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [App\Http\Controllers\ImportController::class, 'preview'])->name('import.preview');

    // Route untuk Import Excel
    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [App\Http\Controllers\ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/store', [App\Http\Controllers\ImportController::class, 'store'])->name('import.store');

    // Route Cetak & Ekspor Laporan
 Route::get('/export', [App\Http\Controllers\ExportController::class, 'index'])->name('export.index');
 Route::get('/export/excel', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('export.excel');
 Route::get('/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('export.pdf');
});
