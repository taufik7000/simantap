<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
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
        $roles = $this->getRoleNames()->map(fn ($role) => Str::lower($role));

        if ($roles->contains('admin')) {
            return Filament::getPanel('admin')->getUrl();
        }
        if ($roles->contains('petugas')) {
            return Filament::getPanel('petugas')->getUrl();
        }
        if ($roles->contains('warga')) {
            return Filament::getPanel('warga')->getUrl();
        }

        return '/'; // Fallback
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }
        
        if ($panel->getId() === 'petugas') {
            return $this->hasRole(['admin', 'petugas']);
        }
        
        if ($panel->getId() === 'warga') {
            return $this->hasRole('warga');
        }
        
        return false;
    }
}
