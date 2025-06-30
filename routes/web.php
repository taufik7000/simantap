<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;
use App\Http\Controllers\Auth\LoginController;


Route::get('/', function () {
    return view('welcome');
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