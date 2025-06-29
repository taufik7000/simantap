<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class UnifiedLoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount()
    {
        // Jika sudah login, redirect ke panel yang sesuai
        if (Auth::check()) {
            $this->redirectUser(Auth::user());
        }
    }

    public function authenticate()
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', __('filament-panels::pages/auth/login.messages.failed'));
            return;
        }

        $user = Auth::user();
        $this->redirectUser($user);
    }

    protected function redirectUser($user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->intended(Filament::getPanel('admin')->getUrl());
        } elseif ($user->hasRole('petugas')) {
            return redirect()->intended(Filament::getPanel('petugas')->getUrl());
        } else {
            return redirect()->intended(Filament::getPanel('warga')->getUrl());
        }
    }

    public function render()
    {
        return view('livewire.unified-login-page')
            ->layout('components.layouts.guest');
    }
}