<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\UnifiedLoginPage;


Route::get('/login', fn () => redirect()->route('filament.admin.auth.login'));

Route::get('/', function () {
    return view('welcome');
});