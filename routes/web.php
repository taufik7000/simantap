<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\WhatsAppVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::get('/', function () {
    return view('home');
});

// Route untuk menampilkan form login
Route::get('/login', [LoginController::class, 'create'])
    ->middleware('guest')
    ->name('login');

// Route untuk memproses data dari form login
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest')
    ->name('login.store');

// Route untuk logout
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.store');

// Halaman untuk menampilkan form input OTP
Route::get('/whatsapp/verify', [WhatsAppVerificationController::class, 'show'])->name('whatsapp.verification.notice');
// Route untuk memproses OTP yang dimasukkan pengguna
Route::post('/whatsapp/verify', [WhatsAppVerificationController::class, 'verify'])->name('whatsapp.verification.verify');
// kirim ulang OTP
Route::post('/whatsapp/resend', [WhatsAppVerificationController::class, 'resend'])->name('whatsapp.verification.resend');

// Grup Route untuk Lupa Kata Sandi
Route::get('/lupa-kata-sandi', [ForgotPasswordController::class, 'showNikRequestForm'])->name('password.request');
Route::post('/lupa-kata-sandi', [ForgotPasswordController::class, 'sendOtp'])->name('password.nik');

// Tahap 1: Verifikasi OTP
Route::get('/verifikasi-otp/{token}', [ForgotPasswordController::class, 'showOtpForm'])->name('password.verify_otp_form');
Route::post('/verifikasi-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify_otp');

// Tahap 2: Reset Password
Route::get('/reset-kata-sandi/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset_form');
Route::post('/reset-kata-sandi', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');

//Route Download Berkas
Route::get('/secure-download', [BerkasController::class, 'download'])
    ->middleware('auth')
    ->name('secure.download');

// Route Download Formulir
Route::get('/download-formulir-master/{formulirMaster}', [BerkasController::class, 'downloadMaster'])
    ->middleware('auth') // Hanya untuk pengguna yang sudah login
    ->name('formulir-master.download');

// Profile Warga Berkas
Route::get('/download-profile-document', [BerkasController::class, 'downloadProfileDocument'])
    ->middleware('auth') 
    ->name('secure.download.profile');

// Berkas Revisi
Route::middleware(['auth'])->group(function () {
    // Route download berkas revisi
    Route::get('/download/revision', [BerkasController::class, 'downloadRevision'])
        ->name('secure.download.revision');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/petugas/quick-status-update', [App\Http\Controllers\Petugas\QuickActionsController::class, 'quickStatusUpdate'])
        ->name('petugas.quick-status-update');
        
    Route::post('/petugas/quick-revision-action', [App\Http\Controllers\Petugas\QuickActionsController::class, 'quickRevisionAction'])
        ->name('petugas.quick-revision-action');
    
    // Route baru untuk reject dengan alasan
    Route::post('/petugas/quick-revision-reject', [App\Http\Controllers\Petugas\QuickActionsController::class, 'quickRevisionReject'])
        ->name('petugas.quick-revision-reject');
    
    // Download semua berkas permohonan (zip)
    Route::get('/berkas/download-all/{permohonan}', [App\Http\Controllers\BerkasController::class, 'downloadAll'])
        ->name('berkas.download-all');
            
});

Route::get('/bantuan', [KnowledgeBaseController::class, 'index'])->name('kb.index');
Route::get('/bantuan/{slug}', [KnowledgeBaseController::class, 'show'])->name('kb.show');
Route::get('/semua-layanan', [PageController::class, 'semuaLayanan'])->name('layanan.semua');
Route::get('/lacak-permohonan', [PageController::class, 'lacakPermohonan'])->name('lacak.permohonan');