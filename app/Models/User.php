<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use App\Models\TicketMessage;
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

    /**
     * Relasi ke anggota keluarga berdasarkan nomor KK yang sama
     */
    public function anggotaKeluarga(): HasMany
    {
        return $this->hasMany(User::class, 'nomor_kk', 'nomor_kk');
    }

    /**
     * Relasi ke permohonan yang diajukan user
     */
    public function permohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    /**
     * Relasi ke tiket yang dibuat user
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relasi ke tiket yang ditugaskan ke user (untuk petugas)
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Relasi ke pesan tiket yang dikirim user
     */
    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }

    /**
     * Mendapatkan inisial nama untuk avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } elseif (count($words) == 1 && strlen($words[0]) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 2));
        } elseif(count($words) == 1) {
            $initials = strtoupper(substr($words[0], 0, 1));
        }
        
        return $initials;
    }

    /**
     * Mendapatkan alamat lengkap yang terformat
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt_rw ? "RT/RW {$this->rt_rw}" : null,
            $this->desa_kelurahan,
            $this->kecamatan,
            $this->kabupaten,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Mendapatkan umur berdasarkan tanggal lahir
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        
        return $this->tanggal_lahir->age;
    }

    /**
     * Cek apakah user adalah kepala keluarga
     */
    public function isKepalaKeluarga(): bool
    {
        return $this->status_keluarga === 'Kepala Keluarga';
    }

    /**
     * Mendapatkan statistik permohonan user
     */
    public function getPermohonanStatisticsAttribute(): array
    {
        $permohonans = $this->permohonans();
        
        return [
            'total' => $permohonans->count(),
            'baru' => $permohonans->where('status', 'baru')->count(),
            'diproses' => $permohonans->whereIn('status', [
                'sedang_ditinjau', 'verifikasi_berkas', 'diproses'
            ])->count(),
            'selesai' => $permohonans->whereIn('status', ['disetujui', 'selesai'])->count(),
            'ditolak' => $permohonans->where('status', 'ditolak')->count(),
        ];
    }

    /**
     * Mendapatkan statistik tiket user
     */
    public function getTicketStatisticsAttribute(): array
    {
        $tickets = $this->tickets();
        
        return [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
        ];
    }

    /**
     * Mendapatkan permohonan terbaru user
     */
    public function getLatestPermohonanAttribute()
    {
        return $this->permohonans()->latest()->first();
    }

    /**
     * Mendapatkan tiket terbaru user
     */
    public function getLatestTicketAttribute()
    {
        return $this->tickets()->latest()->first();
    }

    /**
     * Cek apakah user memiliki permohonan yang sedang diproses
     */
    public function hasActivePermohonan(): bool
    {
        return $this->permohonans()
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->exists();
    }

    /**
     * Cek apakah user memiliki tiket yang masih terbuka
     */
    public function hasActiveTickets(): bool
    {
        return $this->tickets()
            ->whereIn('status', ['open', 'in_progress'])
            ->exists();
    }

    /**
     * Mendapatkan tingkat aktivitas user
     */
    public function getActivityLevelAttribute(): string
    {
        $totalActivity = $this->permohonans()->count() + $this->tickets()->count();
        
        if ($totalActivity >= 20) {
            return 'very_active';
        } elseif ($totalActivity >= 10) {
            return 'active';
        } elseif ($totalActivity >= 5) {
            return 'moderate';
        } elseif ($totalActivity >= 1) {
            return 'low';
        } else {
            return 'inactive';
        }
    }

    /**
     * Scope untuk user yang sudah diverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope untuk user yang belum diverifikasi
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope untuk user dengan data lengkap
     */
    public function scopeWithCompleteData($query)
    {
        return $query->whereNotNull('nik')
            ->whereNotNull('nomor_kk')
            ->whereNotNull('alamat')
            ->whereNotNull('foto_ktp')
            ->whereNotNull('foto_kk')
            ->whereNotNull('foto_tanda_tangan')
            ->whereNotNull('foto_selfie_ktp');
    }

    /**
     * Scope untuk user berdasarkan desa/kelurahan
     */
    public function scopeByDesa($query, $desa)
    {
        return $query->where('desa_kelurahan', $desa);
    }

    /**
     * Scope untuk user berdasarkan kecamatan
     */
    public function scopeByKecamatan($query, $kecamatan)
    {
        return $query->where('kecamatan', $kecamatan);
    }

    /**
     * Scope untuk user berdasarkan status keluarga
     */
    public function scopeByStatusKeluarga($query, $status)
    {
        return $query->where('status_keluarga', $status);
    }

    /**
     * Scope untuk kepala keluarga saja
     */
    public function scopeKepalaKeluarga($query)
    {
        return $query->where('status_keluarga', 'Kepala Keluarga');
    }

    /**
     * Mendapatkan rekomendasi layanan berdasarkan profil user
     */
    public function getRecommendedServicesAttribute(): array
    {
        $recommendations = [];
        
        // Jika belum ada KTP/NIK
        if (empty($this->nik)) {
            $recommendations[] = 'Pengurusan KTP';
        }
        
        // Jika kepala keluarga dan belum ada KK
        if ($this->isKepalaKeluarga() && empty($this->nomor_kk)) {
            $recommendations[] = 'Pengurusan Kartu Keluarga';
        }
        
        // Jika sudah menikah tapi status perkawinan belum update
        if ($this->status_perkawinan === 'Kawin' && !$this->permohonans()->whereHas('layanan', function($q) {
            $q->where('name', 'like', '%akta%nikah%');
        })->exists()) {
            $recommendations[] = 'Pengurusan Akta Nikah';
        }
        
        return $recommendations;
    }

    /**
     * Mendapatkan skor kepuasan berdasarkan riwayat layanan
     */
    public function getSatisfactionScoreAttribute(): ?float
    {
        // Placeholder untuk sistem rating di masa depan
        // Bisa dihitung berdasarkan feedback, waktu proses, dll
        return null;
    }

    public function getUnreadMessagesCount(): int
    {
        return TicketMessage::whereIn('ticket_id', $this->tickets()->pluck('id'))
            ->where('user_id', '!=', $this->id) // Pesan dari orang lain (petugas)
            ->whereNull('read_at') // Yang belum dibaca
            ->count();
    }
}