<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CkeditorUploadController;
use App\Http\Controllers\Admin\EbookEditorController;
use App\Http\Controllers\Admin\MemberModerationController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\StoragePublicFileController;
use Illuminate\Support\Facades\Route;

Route::middleware('member.access')->group(function () {
    Route::get('/', [EbookController::class, 'index'])->name('ebook.home');
    Route::get('/ebook', [EbookController::class, 'index'])->name('ebook.alias');
    Route::get('/ebook/poin/{slug}', [EbookController::class, 'point'])->name('ebook.point');
});

Route::middleware('guest')->group(function () {
    Route::get('/member/login', [MemberAuthController::class, 'showLogin'])->name('member.login');
    Route::post('/member/login', [MemberAuthController::class, 'login'])->name('member.login.submit');
    Route::get('/member/register', [MemberAuthController::class, 'showRegister'])->name('member.register');
    Route::post('/member/register', [MemberAuthController::class, 'register'])->name('member.register.submit');
});

Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');

Route::get('/storage-public/{path}', [StoragePublicFileController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.public.show');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/admin/setup', [AdminAuthController::class, 'showSetup'])->name('admin.setup');
    Route::post('/admin/setup', [AdminAuthController::class, 'storeSetup'])->name('admin.setup.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', [EbookEditorController::class, 'edit'])->name('admin.editor');
    Route::post('/admin', [EbookEditorController::class, 'update'])->name('admin.editor.update');
    Route::post('/admin/members/{member}/approve', [MemberModerationController::class, 'approve'])->name('admin.members.approve');
    Route::post('/admin/members/{member}/reject', [MemberModerationController::class, 'reject'])->name('admin.members.reject');
    Route::post('/admin/upload-image', [CkeditorUploadController::class, 'store'])->name('admin.ckeditor.upload');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
