<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatasetImportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\SpmDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/berita', [HomeController::class, 'newsIndex'])->name('news.index');
Route::get('/berita/{slug}', [HomeController::class, 'showNews'])->name('news.show');
Route::get('/peta', [MapController::class, 'index'])->name('peta');
Route::get('/peta/data', [MapController::class, 'data'])->name('peta.data');
Route::get('/peta/titik-kebakaran', [MapController::class, 'hotspots'])->name('peta.hotspots');
Route::get('/provinsi/{province}', [MapController::class, 'show'])->name('provinsi.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/spm', [SpmDataController::class, 'index'])->name('spm.index');
    Route::get('/spm/{spmData}/edit', [SpmDataController::class, 'edit'])->name('spm.edit');
    Route::put('/spm/{spmData}', [SpmDataController::class, 'update'])->name('spm.update');
    Route::patch('/spm/{spmData}/publish', [SpmDataController::class, 'publish'])->name('spm.publish');
    Route::patch('/spm/{spmData}/draft', [SpmDataController::class, 'draft'])->name('spm.draft');
    Route::delete('/spm/{spmData}', [SpmDataController::class, 'destroy'])->name('spm.destroy');

    Route::get('/regencies', [SpmDataController::class, 'index'])->name('regencies.index');

    Route::put('/provinces/{province}', [ProvinceController::class, 'update'])->name('provinces.update');
    Route::get('/provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('provinces.edit');
    Route::patch('/provinces/{province}/publish', [ProvinceController::class, 'publish'])->name('provinces.publish');
    Route::patch('/provinces/{province}/draft', [ProvinceController::class, 'draft'])->name('provinces.draft');
    Route::delete('/provinces/{province}', [ProvinceController::class, 'destroy'])->name('provinces.destroy');

    Route::get('/news', [NewsController::class, 'index'])->name('admin.news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('admin.news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('admin.news.store');
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
    Route::put('/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
    Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');

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
});
