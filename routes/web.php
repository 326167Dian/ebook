<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CkeditorUploadController;
use App\Http\Controllers\Admin\EbookEditorController;
use App\Http\Controllers\Admin\FooterEditorController;
use App\Http\Controllers\Admin\MemberModerationController;
use App\Http\Controllers\Admin\PharmacyLogoController;
use App\Http\Controllers\Admin\ResellerModerationController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\GdcPayWebhookController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\QrisPaymentController;
use App\Http\Controllers\ResellerAuthController;
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
    Route::post('/member/register/qris', [QrisPaymentController::class, 'createForRegistration'])->name('member.register.qris.create');
    Route::post('/member/register/midtrans', [MidtransPaymentController::class, 'createForRegistration'])->name('member.register.midtrans.create');
});

// Cek kode referral/promo secara langsung dari form (AJAX). Dibatasi 30x/menit per IP.
Route::get('/referral/check', [MemberAuthController::class, 'checkReferral'])
    ->middleware('throttle:30,1')
    ->name('referral.check');

Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout');
Route::get('/member/payment', [MemberAuthController::class, 'showPayment'])->name('member.payment');
Route::post('/member/payment', [MemberAuthController::class, 'submitPayment'])->name('member.payment.submit');
Route::post('/member/payment/qris', [QrisPaymentController::class, 'createForPayment'])->name('member.payment.qris.create');
Route::post('/member/payment/midtrans', [MidtransPaymentController::class, 'createForPayment'])->name('member.payment.midtrans.create');

Route::get('/member/qris/{reference}', [QrisPaymentController::class, 'show'])->name('member.qris.show');
Route::get('/member/qris/{reference}/status', [QrisPaymentController::class, 'status'])->name('member.qris.status');

Route::get('/member/midtrans/{orderId}', [MidtransPaymentController::class, 'show'])->name('member.midtrans.show');
Route::get('/member/midtrans/{orderId}/status', [MidtransPaymentController::class, 'status'])->name('member.midtrans.status');

// Server-to-server "QRIS Payment Notification" webhook, registered in the GDC Pay
// partner dashboard (Pengaturan > Integrasi). Must be reachable from the internet.
Route::post('/webhooks/gdcpay/qris-notification', [GdcPayWebhookController::class, 'qrisNotification'])->name('gdcpay.webhook.qris');

// Mirrors GDC's own SNAP token endpoint path, but on OUR domain: GDC calls this to
// authenticate itself to us before delivering the QRIS notification above. Path is
// fixed by the SNAP standard (/v1.0/access-token/b2b, no custom prefix).
Route::post('/v1.0/access-token/b2b', [GdcPayWebhookController::class, 'issueAccessToken'])->name('gdcpay.webhook.token');

// Midtrans HTTP notification, registered in Midtrans dashboard (Settings > Configuration >
// Payment Notification URL). Must be reachable from the internet.
Route::post('/webhooks/midtrans/notification', [MidtransWebhookController::class, 'notification'])->name('midtrans.webhook.notification');

Route::get('/storage-public/{path}', [StoragePublicFileController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.public.show');

Route::prefix('reseller')->name('reseller.')->group(function () {
    Route::get('/login', [ResellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ResellerAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [ResellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ResellerAuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [ResellerAuthController::class, 'logout'])->name('logout');
    Route::get('/pending', [ResellerAuthController::class, 'showPending'])->name('pending');

    Route::middleware('reseller.access')->group(function () {
        Route::get('/', [ResellerAuthController::class, 'dashboard'])->name('dashboard');
    });
});

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
    Route::get('/admin/statistik-pengunjung', [EbookEditorController::class, 'stats'])->name('admin.stats');
    Route::get('/admin/daftar-member', [EbookEditorController::class, 'members'])->name('admin.members.index');
    Route::get('/admin/daftar-reseller', [EbookEditorController::class, 'resellers'])->name('admin.resellers.index');
    Route::get('/admin/resellers/{reseller}/members', [EbookEditorController::class, 'resellerMembers'])->name('admin.resellers.members');
    Route::post('/admin/resellers/{reseller}/members/{member}/commission-proof', [EbookEditorController::class, 'uploadResellerCommissionProof'])->name('admin.resellers.commission-proof');
    Route::get('/admin/tema-warna', [EbookEditorController::class, 'editTheme'])->name('admin.theme.edit');
    Route::post('/admin/tema-warna', [EbookEditorController::class, 'updateTheme'])->name('admin.theme.update');
    Route::get('/admin/pengaturan-pembayaran', [EbookEditorController::class, 'editPayment'])->name('admin.payment.edit');
    Route::post('/admin/pengaturan-pembayaran', [EbookEditorController::class, 'updatePayment'])->name('admin.payment.update');
    Route::post('/admin/members/{member}/approve', [MemberModerationController::class, 'approve'])->name('admin.members.approve');
    Route::post('/admin/members/{member}/reject', [MemberModerationController::class, 'reject'])->name('admin.members.reject');
    Route::post('/admin/resellers/{reseller}/approve', [ResellerModerationController::class, 'approve'])->name('admin.resellers.approve');
    Route::post('/admin/resellers/{reseller}/reject', [ResellerModerationController::class, 'reject'])->name('admin.resellers.reject');
    Route::get('/admin/footer', [FooterEditorController::class, 'edit'])->name('admin.footer.edit');
    Route::post('/admin/footer', [FooterEditorController::class, 'update'])->name('admin.footer.update');
    Route::post('/admin/pharmacy-logos', [PharmacyLogoController::class, 'store'])->name('admin.pharmacy-logos.store');
    Route::post('/admin/pharmacy-logos/{pharmacyLogo}', [PharmacyLogoController::class, 'update'])->name('admin.pharmacy-logos.update');
    Route::delete('/admin/pharmacy-logos/{pharmacyLogo}', [PharmacyLogoController::class, 'destroy'])->name('admin.pharmacy-logos.destroy');
    Route::post('/admin/upload-image', [CkeditorUploadController::class, 'store'])->name('admin.ckeditor.upload');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Short referral link, e.g. https://domain.test/1 shows the normal e-book home
// page (so the customer can preview it first) while remembering reseller id 1
// in the session, so it auto-fills "Kode Referral" whenever they later reach
// the member registration form. Placed last so it only catches numeric
// segments not matched by any route above.
Route::get('/{id_reseller}', [EbookController::class, 'index'])
    ->whereNumber('id_reseller')
    ->name('ebook.referral');
