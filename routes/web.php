<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\WebsiteController;


Route::get('/', [WebsiteController::class, 'home'])->name('website.home');
Route::get('/page/{slug}', [WebsiteController::class, 'showPage'])->name('website.page');


Route::prefix('api/website')->group(function () {
    Route::get('/content/{pageKey}', [WebsiteController::class, 'getContent'])->name('api.website.content');
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