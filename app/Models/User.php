<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'verified_at', 'last_login_at', 'last_login_ip',
        'nik', 'nomor_kk', 'nomor_whatsapp', 'alamat',
        'jenis_kelamin', 'agama', 'tempat_lahir', 'tanggal_lahir', 'gol_darah',
        'rt_rw', 'desa_kelurahan', 'kecamatan', 'kabupaten',
        'status_keluarga', 'status_perkawinan', 'pekerjaan', 'pendidikan',
        'foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'tanggal_lahir' => 'date',
    ];

    /**
     * Metode terpusat untuk memeriksa kelengkapan profil.
     * Mengembalikan array berisi status dan daftar field yang kosong.
     * @return array{status: string, missing: array}
     */
   public function getProfileCompletenessStatus(): array
    {
        if ($this->verified_at) {
            return ['status' => 'Terverifikasi', 'missing' => []];
        }

        $requiredFields = [
            'nik' => 'NIK',
            'nomor_kk' => 'Nomor Kartu Keluarga',
            'jenis_kelamin' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'nomor_whatsapp' => 'Nomor Whatsapp',
            
            // Data Kelahiran
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'gol_darah' => 'Golongan Darah',

            // Alamat
            'alamat' => 'Alamat Lengkap',
            'rt_rw' => 'RT/RW',
            'desa_kelurahan' => 'Desa/Kelurahan',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten',
            
            // Informasi Tambahan
            'status_keluarga' => 'Status dalam Keluarga',
            'status_perkawinan' => 'Status Perkawinan',
            'pekerjaan' => 'Pekerjaan',
            'pendidikan' => 'Pendidikan Terakhir',
            
            // Dokumen
            'foto_ktp' => 'Foto KTP',
            'foto_kk' => 'Foto Kartu Keluarga',
            'foto_tanda_tangan' => 'Foto Tanda Tangan',
            'foto_selfie_ktp' => 'Foto Selfie dengan KTP',
        ];

        $missing = [];
        foreach ($requiredFields as $field => $label) {
            if (empty($this->{$field})) {
                $missing[] = $label;
            }
        }

        if (empty($missing)) {
            return ['status' => 'Data Lengkap', 'missing' => []];
        }

        return ['status' => 'Belum Lengkap', 'missing' => $missing];
    }
    
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') { return $this->hasRole('admin'); }
        if ($panel->getId() === 'kadis') { return $this->hasRole('kadis'); }
        if ($panel->getId() === 'petugas') { return $this->hasRole(['admin', 'petugas']); }
        if ($panel->getId() === 'warga') { return $this->hasRole('warga'); }
        return false;
    }

    public function getDashboardUrl(): string
    {
        $roles = $this->getRoleNames()->map(fn ($role) => Str::lower($role));
        if ($roles->contains('admin')) { return route('filament.admin.pages.dashboard'); }
        if ($roles->contains('kadis')) { return route('filament.kadis.pages.dashboard'); }
        if ($roles->contains('petugas')) { return route('filament.petugas.pages.dashboard'); }
        if ($roles->contains('warga')) { return route('filament.warga.pages.dashboard'); }
        return '/';
    }

    public function anggotaKeluarga(): HasMany
    {
        // Kode ini sudah benar, yang salah hanya return type hint-nya
        return $this->hasMany(User::class, 'nomor_kk', 'nomor_kk');
    }

    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }

    //Relasi baru terkait tiket atau chat
    // tiket yang dibuat user
    public function tickets(): HasMany
    {
    return $this->hasMany(Ticket::class);
    }

    // Relasi ke tiket yang ditugaskan ke user
    public function assignedTickets(): HasMany
    {
    return $this->hasMany(Ticket::class, 'assigned_to');
    }

    // tiket yang dikirim kepada user
    public function ticketMessages(): HasMany
    {
    return $this->hasMany(TicketMessage::class);
    }
    
}