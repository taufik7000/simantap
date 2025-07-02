<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'user_id',
        'revision_number',
        'berkas_revisi',
        'catatan_revisi',
        'catatan_petugas',
        'status',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'berkas_revisi' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public const STATUS_OPTIONS = [
        'pending' => 'Menunggu Review',
        'reviewed' => 'Sudah Direview',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
    ];

    protected static function booted(): void
    {
        static::creating(function (PermohonanRevision $revision) {
            if (empty($revision->revision_number)) {
                // Auto-increment revision number per permohonan
                $lastRevision = static::where('permohonan_id', $revision->permohonan_id)
                    ->max('revision_number');
                $revision->revision_number = ($lastRevision ?? 0) + 1;
            }
        });
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }
}