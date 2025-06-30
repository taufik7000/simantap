<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

// Pastikan kelas Anda mengimplementasikan FilamentUser
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nik',
        'nomor_kk',
        'nomor_whatsapp',
        'alamat',
        'foto_ktp',
        'foto_kk',
        'foto_tanda_tangan',
        'foto_selfie_ktp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Metode ini mengontrol akses ke seluruh panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }
        
        if ($panel->getId() === 'kadis') {
            return $this->hasRole('kadis');
        }

        if ($panel->getId() === 'petugas') {
            return $this->hasRole(['admin', 'petugas']);
        }
        
        if ($panel->getId() === 'warga') {
            return $this->hasRole('warga');
        }
        
        return false;
    } // <-- Pastikan kurung kurawal penutup ini ada

    /**
     * Metode ini mengarahkan pengguna setelah login.
     */
    public function getDashboardUrl(): string
    {
        $roles = $this->getRoleNames()->map(fn ($role) => Str::lower($role));

        if ($roles->contains('admin')) {
            return route('filament.admin.pages.dashboard');
        }
        if ($roles->contains('kadis')) {
            return route('filament.kadis.pages.dashboard');
        }
        if ($roles->contains('petugas')) {
            return route('filament.petugas.pages.dashboard');
        }
        if ($roles->contains('warga')) {
            return route('filament.warga.pages.dashboard');
        }

        return '/';
    } // <-- Pastikan kurung kurawal penutup ini ada

    /**
     * Metode ini memberitahu Filament nama tampilan pengguna.
     */
    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    } // <-- Pastikan kurung kurawal penutup ini ada

} // <-- Pastikan kurung kurawal penutup untuk kelas juga ada