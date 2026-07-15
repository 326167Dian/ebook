<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CkeditorUploadController;
use App\Http\Controllers\Admin\EbookEditorController;
use App\Http\Controllers\Admin\FooterEditorController;
use App\Http\Controllers\Admin\MemberModerationController;
use App\Http\Controllers\Admin\PharmacyLogoController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\StoragePublicFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EbookController::class, 'index'])->name('ebook.home');
Route::get('/ebook', [EbookController::class, 'index'])->name('ebook.alias');
Route::get('/ebook/poin/{slug}', [EbookController::class, 'point'])->name('ebook.point');

Route::middleware('guest')->group(function () {
    Route::get('/member/auth/google/redirect', [MemberAuthController::class, 'redirectToGoogle'])->name('member.google.redirect');
    Route::get('/member/auth/google/callback', [MemberAuthController::class, 'handleGoogleCallback'])->name('member.google.callback');

    Route::get('/member/login', [MemberAuthController::class, 'showLogin'])->name('member.login');
    Route::post('/member/login', [MemberAuthController::class, 'login'])->name('member.login.submit');
    Route::get('/member/forgot-password', [MemberAuthController::class, 'showForgotPassword'])->name('member.password.request');
    Route::post('/member/forgot-password', [MemberAuthController::class, 'sendResetLink'])->name('member.password.email');
    Route::get('/member/reset-password/{token}', [MemberAuthController::class, 'showResetPassword'])->name('member.password.reset');
    Route::post('/member/reset-password', [MemberAuthController::class, 'resetPassword'])->name('member.password.update');
    Route::get('/member/register', [MemberAuthController::class, 'showRegister'])->name('member.register');
    Route::post('/member/register', [MemberAuthController::class, 'register'])->name('member.register.submit');
});

Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');
Route::get('/member/payment', [MemberAuthController::class, 'showPayment'])->name('member.payment');
Route::post('/member/payment', [MemberAuthController::class, 'submitPayment'])->name('member.payment.submit');

Route::get('/storage-public/{path}', [StoragePublicFileController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.public.show');

Route::middleware('guest')->group(function () {
    Route::get('/yusuf/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/yusuf/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/admin/setup', [AdminAuthController::class, 'showSetup'])->name('admin.setup');
    Route::post('/admin/setup', [AdminAuthController::class, 'storeSetup'])->name('admin.setup.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.editor'))->name('dashboard');
    Route::get('/admin', [EbookEditorController::class, 'edit'])->name('admin.editor');
    Route::post('/admin', [EbookEditorController::class, 'update'])->name('admin.editor.update');
    Route::post('/admin/members/{member}/approve', [MemberModerationController::class, 'approve'])->name('admin.members.approve');
    Route::post('/admin/members/{member}/reject', [MemberModerationController::class, 'reject'])->name('admin.members.reject');
    Route::get('/admin/footer', [FooterEditorController::class, 'edit'])->name('admin.footer.edit');
    Route::post('/admin/footer', [FooterEditorController::class, 'update'])->name('admin.footer.update');
    Route::post('/admin/pharmacy-logos', [PharmacyLogoController::class, 'store'])->name('admin.pharmacy-logos.store');
    Route::post('/admin/pharmacy-logos/{pharmacyLogo}', [PharmacyLogoController::class, 'update'])->name('admin.pharmacy-logos.update');
    Route::delete('/admin/pharmacy-logos/{pharmacyLogo}', [PharmacyLogoController::class, 'destroy'])->name('admin.pharmacy-logos.destroy');
    Route::post('/admin/upload-image', [CkeditorUploadController::class, 'store'])->name('admin.ckeditor.upload');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
