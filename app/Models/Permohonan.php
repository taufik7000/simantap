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
        'dokumen_diterbitkan_at',
        'dokumen_digital_path'
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
        'dokumen_diterbitkan_at' => 'datetime',
    ];

    /**
     * Status yang tersedia untuk permohonan.
     */
/**
     * Status yang tersedia untuk permohonan, diurutkan berdasarkan alur kerja.
     */
    public const STATUS_OPTIONS = [
        // --- Tahap Awal Pengajuan ---
        'baru'                  => 'Baru Diajukan',
        'sedang_ditinjau'       => 'Dalam Peninjauan',
        
        // --- Tahap Verifikasi oleh Petugas ---
        'verifikasi_berkas'     => 'Proses Verifikasi Berkas',
        'diperbaiki_warga'      => 'Telah Diperbaiki Warga',

        // --- Tahap Pengerjaan oleh Petugas ---
        'menunggu_entri_data'   => 'Menunggu Entri Data',
        'proses_entri'          => 'Dalam Proses Entri Data',
        'entri_data_selesai'    => 'Entri Data Selesai',
        
        // --- Tahap Persetujuan & Penyelesaian ---
        'menunggu_persetujuan'  => 'Menunggu Persetujuan',
        'disetujui'             => 'Disetujui',
        'dokumen_diterbitkan'   => 'Dokumen Diterbitkan',
        'selesai'               => 'Selesai (Siap Diambil/Diunduh)',

        // --- Status Khusus (Loop atau Final) ---
        'butuh_perbaikan'       => 'Butuh Perbaikan (Menunggu Warga)',
        'ditolak'               => 'Ditolak',
        'dibatalkan'            => 'Dibatalkan',
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
        // Logika `creating` Anda tetap sama, tidak perlu diubah.
        static::creating(function (Permohonan $permohonan) {
            if (empty($permohonan->kode_permohonan)) {
                do {
                    $prefix = 'SP';
                    $layananId = str_pad($permohonan->layanan_id, 2, '0', STR_PAD_LEFT);
                    $dateComponent = now()->format('dmy');
                    $userId = $permohonan->user_id;
                    $randomChars = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(2));
                    $kode = $prefix . $layananId . $dateComponent . $userId . $randomChars;
                } while (self::where('kode_permohonan', $kode)->exists());
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


        // ===============================================
        // AWAL BLOK UPDATING YANG DIPERBARUI
        // ===============================================
        static::updating(function (Permohonan $permohonan) {
            // Cek jika kolom 'status' benar-benar berubah
            if ($permohonan->isDirty('status')) {
                
                // 1. Membuat log aktivitas (logika lama Anda yang sudah benar)
                $permohonan->logs()->create([
                    'status' => $permohonan->getDirty()['status'],
                    'catatan' => $permohonan->catatan_petugas ?? 'Status diperbarui.',
                    'user_id' => \Illuminate\Support\Facades\Auth::id() ?? $permohonan->user_id,
                ]);

                // 2. Kirim Notifikasi WhatsApp
                $settings = \App\Models\WhatsAppSetting::first();
                $user = $permohonan->user;

                // Kirim notifikasi hanya jika fitur diaktifkan dan template status ada
                if ($settings && $settings->verification_enabled && $settings->status_template_name && $user->nomor_whatsapp) {
                    try {
                        $statusLabel = self::STATUS_OPTIONS[$permohonan->status] ?? $permohonan->status;
                        $note = $permohonan->catatan_petugas ?: 'Tidak ada catatan tambahan dari petugas.';

                        \Illuminate\Support\Facades\Http::withToken($settings->access_token)->post(
                            'https://graph.facebook.com/v19.0/' . $settings->phone_number_id . '/messages',
                            [
                                'messaging_product' => 'whatsapp',
                                'to' => $user->nomor_whatsapp,
                                'type' => 'template',
                                'template' => [
                                    'name' => $settings->status_template_name,
                                    'language' => ['code' => 'id'],
                                    'components' => [
                                        // Komponen header dengan gambar
                                        [
                                            'type' => 'header',
                                            'parameters' => [
                                                [
                                                    'type' => 'image',
                                                    'image' => [
                                                        'id' => '24163273539958957' 
                                                    ]
                                                ]
                                            ]
                                        ],
                                        // Komponen body
                                        [
                                            'type' => 'body',
                                            'parameters' => [
                                                ['type' => 'text', 'text' => $user->name],
                                                ['type' => 'text', 'text' => $permohonan->kode_permohonan],
                                                ['type' => 'text', 'text' => $statusLabel],
                                                ['type' => 'text', 'text' => $note]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        );
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Status Update WhatsApp Gagal Terkirim: ' . $e->getMessage());
                    }
                }
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
        $petugasList = User::role(['petugas',])->get();
        
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

        $petugasList = User::role(['petugas'])->get();
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