<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;


Route::get('/login', UnifiedLoginPage::class)
    ->middleware('guest')
    ->name('login');


Route::get('/', function () {
    return view('welcome');
});