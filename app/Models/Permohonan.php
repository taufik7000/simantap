<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_pemohon' => 'array',
        'berkas_pemohon' => 'array',
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
                    // Ambil nomor dari kode terakhir dan tambahkan 1
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
}