<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Facades\Filament;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getDashboardUrl(): string
    {
        if ($this->hasRole('Admin')) {
            return Filament::getPanel('admin')->getPageUrl('dashboard');
        }

        if ($this->hasRole('Petugas')) {
            return Filament::getPanel('petugas')->getPageUrl('dashboard');
        }

        // Jika tidak ada peran di atas, default-nya adalah panel warga
        if ($this->hasRole('Warga')) {
            return Filament::getPanel('warga')->getPageUrl('dashboard');
        }

        // Fallback jika pengguna tidak memiliki peran apa pun
        return '/';
    }
}
