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

    protected $fillable = [
        'user_id', 'layanan_id', 'kode_permohonan', 'data_pemohon', 'berkas_pemohon',
        'status', 'catatan_petugas', 'status_updated_at', 'status_updated_by',
        'assigned_to', 'assigned_at', 'assigned_by',
    ];

    protected $casts = [
        'data_pemohon' => 'array', 'berkas_pemohon' => 'array',
        'status_updated_at' => 'datetime', 'assigned_at' => 'datetime',
    ];

    public const STATUS_OPTIONS = [
        'baru' => 'Baru Diajukan', 'sedang_ditinjau' => 'Sedang Ditinjau',
        'verifikasi_berkas' => 'Verifikasi Berkas', 'diproses' => 'Sedang Diproses',
        'membutuhkan_revisi' => 'Membutuhkan Revisi', 'butuh_perbaikan' => 'Butuh Perbaikan',
        'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai',
    ];

    protected static function booted(): void
    {
        static::creating(function (Permohonan $permohonan) {
            if (empty($permohonan->kode_permohonan)) {
                $prefix = 'SP-' . now()->format('Ymd');
                $lastPermohonan = self::where('kode_permohonan', 'like', $prefix . '%')->latest('id')->first();
                $nextNumber = $lastPermohonan ? (int) substr($lastPermohonan->kode_permohonan, -4) + 1 : 1;
                $permohonan->kode_permohonan = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });

        static::created(function (Permohonan $permohonan) {
            $permohonan->logs()->create([
                'status' => $permohonan->status,
                'catatan' => 'Permohonan berhasil diajukan oleh warga.',
                'user_id' => $permohonan->user_id,
            ]);
        });

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

    public function logs(): HasMany
    {
        return $this->hasMany(PermohonanLog::class)->orderBy('created_at', 'asc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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
    // ASSIGNMENT & STATUS METHODS
    //=====================================================

    public function assignTo(int $petugasId, ?int $assignedById = null): bool
    {
        $this->update([
            'assigned_to' => $petugasId,
            'assigned_at' => now(),
            'assigned_by' => $assignedById ?? Auth::id(),
        ]);
        return true;
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

    public function autoAssign(): bool
    {
        if ($this->isAssigned()) return false;
        $petugas = static::getPetugasWithLightestWorkload();
        if (!$petugas) return false;
        return $this->assignTo($petugas->id, null);
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }
    
    public function isAssignedTo(int $userId): bool
    {
        return $this->assigned_to === $userId;
    }
    
    public function canBeAssignedTo(?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user || !$user->hasAnyRole(['petugas', 'admin', 'kadis'])) return false;
        if ($this->isAssigned() && !$this->isAssignedTo($user->id)) {
            return $user->hasAnyRole(['admin', 'kadis']);
        }
        return true;
    }

    public function isAssignmentOverdue(int $maxHours = 72): bool
    {
        return $this->assignment_duration !== null && $this->assignment_duration > $maxHours;
    }

    public function getAssignmentDurationAttribute(): ?int
    {
        return $this->assigned_at ? $this->assigned_at->diffInHours(now()) : null;
    }
    
    //=====================================================
    // SCOPES & STATISTICS
    //=====================================================

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }
    
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOverdueAssignment($query, int $maxHours = 72)
    {
        return $query->whereNotNull('assigned_at')
            ->where('assigned_at', '<', now()->subHours($maxHours))
            ->whereNotIn('status', ['selesai', 'ditolak']);
    }

    public static function getPetugasWorkload(int $petugasId): int
    {
        return static::where('assigned_to', $petugasId)
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->count();
    }
    
    public static function getPetugasWithLightestWorkload(): ?User
    {
        $petugasList = User::role(['petugas', 'admin'])->get();
        if ($petugasList->isEmpty()) return null;

        return $petugasList->mapWithKeys(function ($petugas) {
            return [$petugas->id => static::getPetugasWorkload($petugas->id)];
        })->sort()->keys()->map(fn($id) => $petugasList->find($id))->first();
    }
}