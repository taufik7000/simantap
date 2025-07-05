<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layanan extends Model
{
    use HasFactory;
    
    protected $table = 'layanan';
    
    protected $fillable = [
        'kategori_layanan_id', 
        'name', 
        'slug', 
        'deskripsi_layanan',
        'description', 
        'is_active', 
        'icon'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'description' => 'array',
    ];

    /**
     * Relasi ke kategori layanan
     */
    public function kategoriLayanan(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class);
    }

    /**
     * Relasi ke formulir master
     */
    public function formulirMaster(): BelongsTo
    {
        return $this->belongsTo(FormulirMaster::class);
    }

    /**
     * Relasi ke permohonan yang menggunakan layanan ini
     */
    public function permohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    /**
     * Relasi ke tiket yang terkait dengan layanan ini
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Mendapatkan tiket aktif untuk layanan ini
     */
    public function activeTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Mendapatkan permohonan yang masih aktif
     */
    public function activePermohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class)
            ->whereNotIn('status', ['selesai', 'ditolak']);
    }

    /**
     * Mendapatkan jumlah tiket yang masih terbuka untuk layanan ini
     */
    public function getOpenTicketsCountAttribute(): int
    {
        return $this->activeTickets()->count();
    }

    /**
     * Mendapatkan jumlah permohonan aktif
     */
    public function getActivePermohonansCountAttribute(): int
    {
        return $this->activePermohonans()->count();
    }

    /**
     * Mendapatkan statistik tiket untuk layanan ini
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
            'urgent' => $tickets->where('priority', 'urgent')->count(),
            'high' => $tickets->where('priority', 'high')->count(),
        ];
    }

    /**
     * Mendapatkan statistik permohonan untuk layanan ini
     */
    public function getPermohonanStatisticsAttribute(): array
    {
        $permohonans = $this->permohonans();
        
        return [
            'total' => $permohonans->count(),
            'baru' => $permohonans->where('status', 'baru')->count(),
            'diproses' => $permohonans->whereIn('status', ['sedang_ditinjau', 'verifikasi_berkas', 'diproses'])->count(),
            'disetujui' => $permohonans->where('status', 'disetujui')->count(),
            'selesai' => $permohonans->where('status', 'selesai')->count(),
            'ditolak' => $permohonans->where('status', 'ditolak')->count(),
        ];
    }

    /**
     * Scope untuk layanan yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk layanan yang memiliki tiket aktif
     */
    public function scopeWithActiveTickets($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', ['open', 'in_progress']);
        });
    }

    /**
     * Scope untuk layanan yang memiliki permohonan aktif
     */
    public function scopeWithActivePermohonans($query)
    {
        return $query->whereHas('permohonans', function ($q) {
            $q->whereNotIn('status', ['selesai', 'ditolak']);
        });
    }

    /**
     * Scope untuk layanan yang banyak ditanyakan (berdasarkan jumlah tiket)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->limit($limit);
    }

    /**
     * Scope untuk layanan berdasarkan kategori
     */
    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_layanan_id', $kategoriId);
    }

    /**
     * Mendapatkan nama lengkap layanan dengan kategori
     */
    public function getFullNameAttribute(): string
    {
        return $this->kategoriLayanan->name . ' - ' . $this->name;
    }

    /**
     * Cek apakah layanan memiliki formulir master
     */
    public function hasFormulirMaster(): bool
    {
        if (!$this->description || !is_array($this->description)) {
            return false;
        }

        foreach ($this->description as $syarat) {
            if (!empty($syarat['formulir_master_id'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mendapatkan daftar formulir master yang diperlukan
     */
    public function getRequiredFormulirMastersAttribute(): array
    {
        $formulirIds = [];
        
        if ($this->description && is_array($this->description)) {
            foreach ($this->description as $syarat) {
                if (!empty($syarat['formulir_master_id'])) {
                    $ids = (array) $syarat['formulir_master_id'];
                    $formulirIds = array_merge($formulirIds, $ids);
                }
            }
        }

        return array_unique($formulirIds);
    }

    /**
     * Mendapatkan tingkat kesulitan berdasarkan jumlah persyaratan
     */
    public function getDifficultyLevelAttribute(): string
    {
        if (!$this->description || !is_array($this->description)) {
            return 'mudah';
        }

        $jumlahSyarat = count($this->description);
        
        if ($jumlahSyarat <= 2) {
            return 'mudah';
        } elseif ($jumlahSyarat <= 4) {
            return 'sedang';
        } else {
            return 'sulit';
        }
    }

    /**
     * Mendapatkan estimasi waktu proses berdasarkan kompleksitas
     */
    public function getEstimatedProcessingTimeAttribute(): string
    {
        $difficultyLevel = $this->difficulty_level;
        
        return match($difficultyLevel) {
            'mudah' => '1-3 hari kerja',
            'sedang' => '3-7 hari kerja',
            'sulit' => '7-14 hari kerja',
            default => '1-7 hari kerja',
        };
    }

    /**
     * Cek apakah layanan sering bermasalah (banyak tiket)
     */
    public function isProblematic(): bool
    {
        $totalPermohonans = $this->permohonans()->count();
        $totalTickets = $this->tickets()->count();
        
        if ($totalPermohonans == 0) {
            return false;
        }
        
        // Jika ratio tiket:permohonan > 0.3 (30%), dianggap bermasalah
        return ($totalTickets / $totalPermohonans) > 0.3;
    }

    /**
     * Mendapatkan rata-rata rating kepuasan (jika ada sistem rating)
     */
    public function getAverageRatingAttribute(): ?float
    {
        // Placeholder untuk sistem rating di masa depan
        // Bisa dihubungkan dengan tabel ratings atau feedback
        return null;
    }
}