<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Permohonan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'layanan_id',
        'kode_permohonan',
        'data_pemohon',
        'berkas_pemohon',
        'status',
        'catatan_petugas',
        'status_updated_at',
        'status_updated_by',
        'assigned_to', 
        'assigned_at', 
        'assigned_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_pemohon' => 'array',
        'berkas_pemohon' => 'array',
        'status_updated_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    /**
     * Status yang tersedia untuk permohonan.
     */
    public const STATUS_OPTIONS = [
        'baru' => 'Baru Diajukan',
        'menunggu_verifikasi' => 'Menunggu Verifikasi Berkas',
        'proses_verifikasi' => 'Proses Verifikasi Berkas',
        'proses_entri' => 'Dalam Proses Entri Data',
        'entri_data_selesai' => 'Entri Data Selesai',
        'menunggu_persetujuan' => 'Menunggu Persetujuan',
        'disetujui' => 'Disetujui',
        'dokumen_diterbitkan' => 'Dokumen Diterbitkan',
        'proses_pengiriman' => 'Dokumen Dalam Proses Pengiriman',
        'selesai' => 'Selesai (Siap Diambil/Diunduh)',
        'butuh_perbaikan' => 'Membutuhkan Revisi',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
        'diperbaiki_warga' => 'Diperbaiki oleh Warga',
    ];

    public function getAllowedTransitions(): array
    {
        

        // Admin atau Kadis bisa mengubah ke status manapun jika diperlukan
        if (Auth::user()?->hasAnyRole(['admin', 'kadis'])) {
            return array_diff(array_keys(self::STATUS_OPTIONS), [$this->status]);
        }
        
        return $transitions[$this->status] ?? [];
    }

    public function getNextStatusOptions(): array
    {
        $allowed = $this->getAllowedTransitions();
        return collect(self::STATUS_OPTIONS)
            ->filter(fn($value, $key) => in_array($key, $allowed))
            ->toArray();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Permohonan $permohonan) {
            if (empty($permohonan->kode_permohonan)) {
                // Terus buat kode baru untuk menjamin keunikan jika terjadi duplikasi (sangat jarang terjadi)
                do {
                    // 1. Prefix Statis
                    $prefix = 'SP';

                    // 2. ID Layanan (Layanan ID)
                    // Mengambil layanan_id langsung dari model permohonan yang sedang dibuat.
                    // Diberi padding '0' di depan jika ID kurang dari 2 digit (misal: 1 -> 01, 15 -> 15)
                    $layananId = str_pad($permohonan->layanan_id, 2, '0', STR_PAD_LEFT);

                    // 3. Tanggal, Bulan, dan Tahun
                    // Format: ddmmyyyy (contoh: 07072024)
                    $dateComponent = now()->format('dmy');

                    // 4. ID Pengguna (User ID)
                    // Mengambil user_id dari model permohonan. Diasumsikan user_id selalu ada.
                    $userId = $permohonan->user_id;

                    // 5. Dua Huruf Acak (A-Z)
                    $randomChars = Str::upper(Str::random(2));

                    // 6. Gabungkan semua komponen menjadi satu kode
                    $kode = $prefix . $layananId . $dateComponent . $userId . $randomChars;

                } while (self::where('kode_permohonan', $kode)->exists());

                // 7. Tetapkan kode unik ke model
                $permohonan->kode_permohonan = $kode;
            }
        });

        // Membuat log pertama saat permohonan berhasil dibuat
        static::created(function (Permohonan $permohonan) {
            $permohonan->logs()->create([
                'status' => $permohonan->status,
                'catatan' => 'Permohonan berhasil diajukan oleh warga.',
                'user_id' => $permohonan->user_id,
            ]);
        });

        // Membuat log setiap kali status permohonan diubah
        static::updating(function (Permohonan $permohonan) {
            if ($permohonan->isDirty('status')) {
                $permohonan->logs()->create([
                    'status' => $permohonan->getDirty()['status'],
                    'catatan' => $permohonan->catatan_petugas ?? 'Status diperbarui.',
                    'user_id' => Auth::id(),
                ]);
            }
        });
    }

    //=====================================================
    // ROUTE MODEL BINDING METHODS  
    //=====================================================

    /**
     * Mendapatkan route key name untuk binding
     */
    public function getRouteKeyName(): string
    {
        return 'kode_permohonan';
    }

    /**
     * Custom route model binding untuk kode_permohonan
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Jika menggunakan kode_permohonan sebagai route key
        if ($field === 'kode_permohonan' || $this->getRouteKeyName() === 'kode_permohonan') {
            return $this->where('kode_permohonan', $value)->first();
        }
        
        // Fallback ke default behavior untuk field lain
        return parent::resolveRouteBinding($value, $field);
    }

    //=====================================================
    // RELATIONSHIPS
    //=====================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PermohonanLog::class)->orderBy('created_at', 'asc');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PermohonanRevision::class)->orderBy('revision_number', 'desc');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    //=====================================================
    // HELPER METHODS
    //=====================================================

    public function assignTo(int $petugasId, ?int $assignedById = null): bool
    {
        return $this->update([
            'assigned_to' => $petugasId,
            'assigned_at' => now(),
            'assigned_by' => $assignedById ?? Auth::id(),
        ]);
    }

    public function reassignTo(int $newPetugasId, ?string $reason = null): bool
    {
        return $this->assignTo($newPetugasId, Auth::id());
    }

    public function unassign(?string $reason = null): bool
    {
        return $this->update([
            'assigned_to' => null,
            'assigned_at' => null,
            'assigned_by' => null,
        ]);
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function isAssignedTo(int $userId): bool
    {
        return $this->assigned_to === $userId;
    }

    /**
     * MISSING METHOD - Cek apakah permohonan bisa ditugaskan
     */
    public function canBeAssignedTo(): bool
    {
        // Permohonan bisa ditugaskan jika:
        // 1. Belum ditugaskan ke siapa pun
        // 2. Statusnya bukan selesai atau ditolak
        return is_null($this->assigned_to) && !in_array($this->status, ['selesai', 'ditolak']);
    }

    public function canBeRevised(): bool
    {
        return in_array($this->status, ['membutuhkan_revisi', 'butuh_perbaikan']);
    }

    public function hasActiveRevision(): bool
    {
        return $this->revisions()->where('status', 'pending')->exists();
    }

    /**
     * MISSING METHOD - Cek apakah permohonan memiliki tiket aktif
     */
    public function hasActiveTickets(): bool
    {
        return $this->tickets()
            ->whereIn('status', ['open', 'in_progress'])
            ->exists();
    }

    /**
     * MISSING METHOD - Mendapatkan tiket aktif
     */
    public function activeTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->whereIn('status', ['open', 'in_progress']);
    }
    
    public function getAssignmentDurationAttribute(): ?int
    {
        return $this->assigned_at ? $this->assigned_at->diffInHours(now()) : null;
    }

    public function isAssignmentOverdue(int $maxHours = 72): bool
    {
        return $this->assignment_duration !== null && $this->assignment_duration > $maxHours;
    }

    //=====================================================
    // STATIC METHODS FOR WIDGETS & STATISTICS
    //=====================================================

    public static function getAssignmentStatistics(): array
    {
        return [
            'total_permohonan' => static::count(),
            'belum_ditugaskan' => static::whereNull('assigned_to')->count(),
            'sudah_ditugaskan' => static::whereNotNull('assigned_to')->count(),
            'overdue_assignment' => static::whereNotNull('assigned_at')->where('assigned_at', '<', now()->subHours(72))->whereNotIn('status', ['selesai', 'ditolak'])->count(),
        ];
    }

    public static function getPetugasWorkload(int $petugasId): int
    {
        return static::where('assigned_to', $petugasId)
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->count();
    }

    public static function getWorkloadDistribution(): array
    {
        $petugasList = User::role(['petugas', 'admin'])->get();
        
        return $petugasList->map(function ($petugas) {
            return [
                'petugas_name' => $petugas->name,
                'active_count' => static::getPetugasWorkload($petugas->id),
            ];
        })->toArray();
    }

    public function autoAssign(): bool
    {
        if ($this->isAssigned()) {
            return false;
        }

        $petugasList = User::role(['petugas', 'admin'])->get();
        if ($petugasList->isEmpty()) {
            return false;
        }

        $lightestPetugas = $petugasList->mapWithKeys(function ($petugas) {
            return [$petugas->id => static::getPetugasWorkload($petugas->id)];
        })->sort()->keys()->first();
        
        if (!$lightestPetugas) {
            return false;
        }

        return $this->assignTo($lightestPetugas, null);
    }

    //=====================================================
    // MISSING SCOPE METHODS
    //=====================================================

    /**
     * Scope untuk permohonan yang belum ditugaskan
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope untuk permohonan overdue assignment
     */
    public function scopeOverdueAssignment(Builder $query, int $maxHours = 72): Builder
    {
        return $query->whereNotNull('assigned_at')
            ->where('assigned_at', '<', now()->subHours($maxHours))
            ->whereNotIn('status', ['selesai', 'ditolak']);
    }
}