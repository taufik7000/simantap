<?php

namespace App\Models;

// 1. PASTIKAN SEMUA IMPORT INI ADA
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

// 2. PASTIKAN KELAS ANDA MENGIMPLEMENTASIKAN FilamentUser
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

    // 3. INI ADALAH METODE YANG AKAN DIPANGGIL FILAMENT
    // Ini menggantikan logika default yang mencari kolom 'name'.


    // 4. METODE INI JUGA DIBUTUHKAN OLEH FilamentUser
    // Ini adalah satu-satunya tempat untuk mengatur hak akses panel.
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
    
    // 5. METODE INI ADALAH "OTAK" PENGALIHAN SETELAH LOGIN
    public function getDashboardUrl(): string
    {
        $roles = $this->getRoleNames()->map(fn ($role) => Str::lower($role));

        if ($roles->contains('admin')) {
            return route('filament.admin.pages.dashboard');
        }
        if ($roles->contains('petugas')) {
            return route('filament.petugas.pages.dashboard');
        }
        if ($roles->contains('warga')) {
            return route('filament.warga.pages.dashboard');
        }

        return '/';
    }
}