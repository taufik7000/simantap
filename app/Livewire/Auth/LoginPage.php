<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BasePage;

class LoginPage extends BasePage
{
    /**
     * @return array<int, Component>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        Filament::auth()->login($user);

        session()->regenerate();
        
        // Arahkan pengguna menggunakan metode yang akan kita buat di model User
        return app(LoginResponse::class, ['request' => request()])
            ->setRedirectUrl(Filament::getUserDefaultUrl(user: $user));
    }
}