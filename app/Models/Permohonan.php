<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    /**
     * Status yang tersedia untuk permohonan
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
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Event ini akan berjalan SEBELUM data baru disimpan ke database
        static::creating(function (Permohonan $permohonan) {
            // Jika kode permohonan belum di-set, buat yang baru
            if (empty($permohonan->kode_permohonan)) {
                // Format: SP-YYYYMMDD-XXXX (4 digit angka urut)
                $prefix = 'SP-' . now()->format('Ymd');
                
                // Cari permohonan terakhir hari ini untuk mendapatkan nomor urut berikutnya
                $lastPermohonan = self::where('kode_permohonan', 'like', $prefix . '%')->latest('id')->first();
                
                $nextNumber = 1;
                if ($lastPermohonan) {
                    $lastNumber = (int) substr($lastPermohonan->kode_permohonan, -4);
                    $nextNumber = $lastNumber + 1;
                }

                // Gabungkan menjadi kode unik, contoh: SP-20240701-0001
                $permohonan->kode_permohonan = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });

        // Event yang berjalan SETELAH data diupdate
        static::updated(function (Permohonan $permohonan) {
            // Log perubahan status untuk audit trail
            if ($permohonan->wasChanged('status')) {
                \Log::info('Status permohonan berubah', [
                    'kode_permohonan' => $permohonan->kode_permohonan,
                    'status_lama' => $permohonan->getOriginal('status'),
                    'status_baru' => $permohonan->status,
                    'catatan_petugas' => $permohonan->catatan_petugas,
                    'updated_by' => auth()->user()?->name ?? 'System',
                ]);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'kode_permohonan';
    }

    /**
     * Mendapatkan data user (warga) yang mengajukan permohonan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan data sub layanan yang dipilih.
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Relasi ke tiket yang terkait dengan permohonan
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Mendapatkan tiket aktif untuk permohonan
     */
    public function activeTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Cek apakah permohonan memiliki tiket aktif
     */
    public function hasActiveTickets(): bool
    {
        return $this->activeTickets()->exists();
    }

    /**
     * Mendapatkan warna badge untuk status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'gray',
            'sedang_ditinjau' => 'blue',
            'verifikasi_berkas' => 'yellow',
            'diproses' => 'blue',
            'membutuhkan_revisi' => 'red',
            'butuh_perbaikan' => 'red',
            'disetujui' => 'green',
            'ditolak' => 'red',
            'selesai' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Mendapatkan label status yang user-friendly
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    /**
     * Mendapatkan durasi proses permohonan
     */
    public function getProcessingTimeAttribute(): ?int
    {
        if ($this->status === 'selesai' && $this->status_updated_at) {
            return $this->created_at->diffInDays($this->status_updated_at);
        }
        
        // Jika belum selesai, hitung dari created_at sampai sekarang
        return $this->created_at->diffInDays(now());
    }

    /**
     * Cek apakah permohonan sudah selesai diproses
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['disetujui', 'selesai']);
    }

    /**
     * Cek apakah permohonan ditolak atau perlu perbaikan
     */
    public function isRejectedOrNeedsFix(): bool
    {
        return in_array($this->status, ['ditolak', 'membutuhkan_revisi', 'butuh_perbaikan']);
    }

    /**
     * Cek apakah permohonan sedang dalam proses
     */
    public function isInProcess(): bool
    {
        return in_array($this->status, ['sedang_ditinjau', 'verifikasi_berkas', 'diproses']);
    }

    /**
     * Cek apakah permohonan dapat dibatalkan
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['baru', 'sedang_ditinjau', 'verifikasi_berkas']);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk permohonan yang perlu ditinjau
     */
    public function scopeNeedReview($query)
    {
        return $query->whereIn('status', ['baru', 'sedang_ditinjau', 'verifikasi_berkas']);
    }

    /**
     * Scope untuk permohonan yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['disetujui', 'selesai']);
    }

    /**
     * Scope untuk permohonan yang ditolak atau butuh perbaikan
     */
    public function scopeRejectedOrNeedsFix($query)
    {
        return $query->whereIn('status', ['ditolak', 'membutuhkan_revisi', 'butuh_perbaikan']);
    }

    /**
     * Scope untuk permohonan berdasarkan layanan
     */
    public function scopeByLayanan($query, $layananId)
    {
        return $query->where('layanan_id', $layananId);
    }

    /**
     * Scope untuk permohonan berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk permohonan dalam rentang tanggal
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Mendapatkan persentase kelengkapan berkas
     */
    public function getBerkasCompletenessAttribute(): float
    {
        if (!$this->berkas_pemohon || !is_array($this->berkas_pemohon)) {
            return 0;
        }

        $totalBerkas = count($this->berkas_pemohon);
        $berkasLengkap = 0;

        foreach ($this->berkas_pemohon as $berkas) {
            if (!empty($berkas['path_dokumen'])) {
                $berkasLengkap++;
            }
        }

        return $totalBerkas > 0 ? ($berkasLengkap / $totalBerkas) * 100 : 0;
    }

    /**
     * Cek apakah permohonan sudah lewat batas waktu normal
     */
    public function isOverdue(): bool
    {
        $standardProcessingDays = 7; // 7 hari kerja
        $workingDaysElapsed = $this->getWorkingDaysElapsed();
        
        return $workingDaysElapsed > $standardProcessingDays && !$this->isCompleted();
    }

    /**
     * Menghitung hari kerja yang sudah berlalu
     */
    private function getWorkingDaysElapsed(): int
    {
        $start = $this->created_at;
        $end = now();
        $workingDays = 0;

        while ($start->lte($end)) {
            // Skip weekend (Saturday = 6, Sunday = 0)
            if (!in_array($start->dayOfWeek, [0, 6])) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Mendapatkan estimasi tanggal selesai
     */
    public function getEstimatedCompletionDateAttribute(): \Carbon\Carbon
    {
        $standardProcessingDays = 7;
        $date = $this->created_at->copy();
        $addedDays = 0;

        while ($addedDays < $standardProcessingDays) {
            $date->addDay();
            // Skip weekend
            if (!in_array($date->dayOfWeek, [0, 6])) {
                $addedDays++;
            }
        }

        return $date;
    }

    /**
     * Mendapatkan prioritas berdasarkan urgency dan complexity
     */
    public function getPriorityLevelAttribute(): string
    {
        // Urgent jika sudah overdue
        if ($this->isOverdue()) {
            return 'urgent';
        }

        // High jika mendekati batas waktu
        $workingDaysElapsed = $this->getWorkingDaysElapsed();
        if ($workingDaysElapsed >= 5) {
            return 'high';
        }

        // Medium jika ada tiket aktif (ada masalah)
        if ($this->hasActiveTickets()) {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Mendapatkan next action yang perlu dilakukan
     */
    public function getNextActionAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Menunggu peninjauan petugas',
            'sedang_ditinjau' => 'Sedang dalam peninjauan',
            'verifikasi_berkas' => 'Verifikasi kelengkapan berkas',
            'diproses' => 'Sedang diproses oleh petugas',
            'membutuhkan_revisi' => 'Memerlukan revisi dari pemohon',
            'butuh_perbaikan' => 'Memerlukan perbaikan dokumen',
            'disetujui' => 'Menunggu penerbitan dokumen',
            'ditolak' => 'Permohonan ditolak',
            'selesai' => 'Permohonan selesai diproses',
            default => 'Status tidak diketahui',
        };
    }

public function revisions(): HasMany
{
    return $this->hasMany(PermohonanRevision::class)->orderBy('revision_number', 'desc');
}

public function latestRevision()
{
    return $this->hasOne(PermohonanRevision::class)->latestOfMany('revision_number');
}

public function canBeRevised(): bool
{
    return in_array($this->status, ['membutuhkan_revisi', 'butuh_perbaikan']);
}

public function hasActiveRevision(): bool
{
    return $this->revisions()->where('status', 'pending')->exists();
}
}