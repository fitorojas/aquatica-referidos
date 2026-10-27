<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BenefitHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DuplicateAuditController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ReferralManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Public\ReferralRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (referidos.aquatica.cr)
|--------------------------------------------------------------------------
*/
Route::get('/', [ReferralRegistrationController::class, 'create'])->name('public.referral.create');
Route::post('/referidos', [ReferralRegistrationController::class, 'store'])->name('public.referral.store');
Route::get('/referidos/exito/{code}', [ReferralRegistrationController::class, 'success'])->name('public.referral.success');
Route::get('/referidos/estado', [ReferralRegistrationController::class, 'track'])->name('public.referral.track');

/*
|--------------------------------------------------------------------------
| Rutas Administrativas (referidos.aquatica.cr/admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Autenticación
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Panel Privado Protegido
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Expedientes y CRM Pipeline
        Route::get('/referrals', [ReferralManagementController::class, 'index'])->name('admin.referrals.index');
        Route::get('/referrals/{referral}', [ReferralManagementController::class, 'show'])->name('admin.referrals.show');
        Route::post('/referrals/{referral}/status', [ReferralManagementController::class, 'updateStatus'])->name('admin.referrals.update_status');
        Route::post('/referrals/{referral}/activity', [ReferralManagementController::class, 'storeActivity'])->name('admin.referrals.store_activity');

        // Verificaciones Manuales en 3 Pasos
        Route::post('/referrals/{referral}/verify/eligibility', [VerificationController::class, 'verifyEligibility'])->name('admin.verify.eligibility');
        Route::post('/referrals/{referral}/verify/enrollment', [VerificationController::class, 'verifyEnrollment'])->name('admin.verify.enrollment');
        Route::post('/referrals/{referral}/verify/payment', [VerificationController::class, 'verifyPayment'])->name('admin.verify.payment');

        // Historial de Beneficios y Descuentos (₡30.800)
        Route::get('/benefits', [BenefitHistoryController::class, 'index'])->name('admin.benefits.index');
        Route::post('/benefits/{benefit}/apply', [BenefitHistoryController::class, 'applyDiscount'])->name('admin.benefits.apply');

        // Auditoría de Duplicados
        Route::get('/duplicates', [DuplicateAuditController::class, 'index'])->name('admin.duplicates.index');
        Route::post('/duplicates/{detection}/resolve', [DuplicateAuditController::class, 'resolve'])->name('admin.duplicates.resolve');

        // Exportaciones
        Route::get('/exports/referrals', [ExportController::class, 'referrals'])->name('admin.exports.referrals');

        // Configuración y Usuarios (Super Admin)
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::post('/settings/test-evolution', [SettingController::class, 'testEvolution'])->name('admin.settings.test_evolution');

        Route::resource('/users', UserController::class)->names('admin.users');
    });
});
