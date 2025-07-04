<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerkasController;


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

//Route Download Berkas
Route::get('/secure-download', [BerkasController::class, 'download'])
    ->middleware('auth')
    ->name('secure.download');

// Route Download Formulir
Route::get('/download-formulir-master/{formulirMaster}', [BerkasController::class, 'downloadMaster'])
    ->middleware('auth') // Hanya untuk pengguna yang sudah login
    ->name('formulir-master.download');

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