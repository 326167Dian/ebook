<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CkeditorUploadController;
use App\Http\Controllers\Admin\EbookEditorController;
use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EbookController::class, 'index'])->name('ebook.home');
Route::get('/ebook', [EbookController::class, 'index'])->name('ebook.alias');
Route::get('/ebook/poin/{slug}', [EbookController::class, 'point'])->name('ebook.point');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/admin/setup', [AdminAuthController::class, 'showSetup'])->name('admin.setup');
    Route::post('/admin/setup', [AdminAuthController::class, 'storeSetup'])->name('admin.setup.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [EbookEditorController::class, 'edit'])->name('admin.editor');
    Route::post('/admin', [EbookEditorController::class, 'update'])->name('admin.editor.update');
    Route::post('/admin/upload-image', [CkeditorUploadController::class, 'store'])->name('admin.ckeditor.upload');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
