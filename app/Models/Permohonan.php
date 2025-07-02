<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

        // Saat permohonan BARU DIBUAT, buat log pertamanya
        static::created(function (Permohonan $permohonan) {
            $permohonan->logs()->create([
                'status' => $permohonan->status,
                'catatan' => 'Permohonan berhasil diajukan oleh warga.',
                'user_id' => $permohonan->user_id,
            ]);
        });

        // Saat permohonan DI-UPDATE, catat perubahan statusnya
        static::updating(function (Permohonan $permohonan) {
            // Cek jika kolom 'status' benar-benar berubah
            if ($permohonan->isDirty('status')) {
                $permohonan->logs()->create([
                    'status' => $permohonan->getDirty()['status'],
                    'catatan' => $permohonan->catatan_petugas ?? 'Status diperbarui.',
                    'user_id' => Auth::id(), // User yang sedang login (petugas/sistem)
                ]);
            }
        });
    }
    
    /**
     * Relasi ke riwayat log.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PermohonanLog::class)->orderBy('created_at', 'asc'); // diurutkan dari yang paling lama
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
     * Cek apakah permohonan dapat direvisi oleh pengguna.
     */
    public function canBeRevised(): bool
    {
        return in_array($this->status, ['membutuhkan_revisi', 'butuh_perbaikan']);
    }

    /**
     * Cek apakah sudah ada revisi yang aktif (menunggu review).
     */
    public function hasActiveRevision(): bool
    {
        return $this->revisions()->where('status', 'pending')->exists();
    }

    /**
     * Relasi ke semua revisi yang pernah dibuat untuk permohonan ini.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(PermohonanRevision::class)->orderBy('revision_number', 'desc');
    }
}