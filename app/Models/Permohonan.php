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
                // Format: P-YYYYMMDD-XXXX (4 digit angka urut)
                $prefix = 'SP-' . now()->format('Ymd');
                
                // Cari permohonan terakhir hari ini untuk mendapatkan nomor urut berikutnya
                $lastPermohonan = self::where('kode_permohonan', 'like', $prefix . '%')->latest('id')->first();
                
                $nextNumber = 1;
                if ($lastPermohonan) {
                    // Ambil nomor dari kode terakhir dan tambahkan 1
                    $lastNumber = (int) substr($lastPermohonan->kode_permohonan, -4);
                    $nextNumber = $lastNumber + 1;
                }

                // Gabungkan menjadi kode unik, contoh: P-20240701-0001
                $permohonan->kode_permohonan = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
        // Target model dan foreign key sudah benar setelah migrasi
        return $this->belongsTo(Layanan::class);
    }
}