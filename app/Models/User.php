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
        'nik',
        'nomor_kk',
        'nama_lengkap',
        'password',
        'nomor_whatsapp',
        'email', 
        'foto_ktp',
        'foto_kk',
        'foto_tanda_tangan',
        'foto_selfie_ktp',
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

        // Periksa peran dengan urutan prioritas
        if ($roles->contains('admin')) {
            // GANTI ->getUrl() MENJADI route(...)
            return route('filament.admin.pages.dashboard');
        }

        if ($roles->contains('petugas')) {
            // GANTI ->getUrl() MENJADI route(...)
            return route('filament.petugas.pages.dashboard');
        }

        if ($roles->contains('warga')) {
            // GANTI ->getUrl() MENJADI route(...)
            return route('filament.warga.pages.dashboard');
        }

        // Fallback jika tidak punya peran
        return '/';
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
