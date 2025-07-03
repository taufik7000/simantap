<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
        'sedang_ditinjau' => 'Sedang Ditinjau',
        'verifikasi_berkas' => 'Verifikasi Berkas',
        'diproses' => 'Sedang Diproses',
        'membutuhkan_revisi' => 'Membutuhkan Revisi',
        'butuh_perbaikan' => 'Butuh Perbaikan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'selesai' => 'Selesai',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Membuat kode permohonan unik saat data baru dibuat
        static::creating(function (Permohonan $permohonan) {
            if (empty($permohonan->kode_permohonan)) {
                $prefix = 'SP-' . now()->format('Ymd');
                $lastPermohonan = self::where('kode_permohonan', 'like', $prefix . '%')->latest('id')->first();
                $nextNumber = $lastPermohonan ? (int) substr($lastPermohonan->kode_permohonan, -4) + 1 : 1;
                $permohonan->kode_permohonan = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function isAssignedTo(int $userId): bool
    {
        return $this->assigned_to === $userId;
    }

    public function canBeRevised(): bool
    {
        return in_array($this->status, ['membutuhkan_revisi', 'butuh_perbaikan']);
    }

    public function hasActiveRevision(): bool
    {
        return $this->revisions()->where('status', 'pending')->exists();
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
}