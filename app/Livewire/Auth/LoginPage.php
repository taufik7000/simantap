<?php

namespace App\Livewire\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BasePage;

class LoginPage extends BasePage
{
    // Cukup biarkan kosong, karena semua logika sudah diwarisi
    // dari BasePage dan metode getFilamentUrl di model User.
}