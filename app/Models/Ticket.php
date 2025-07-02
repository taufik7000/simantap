<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tiket',
        'permohonan_id',
        'layanan_id',
        'user_id',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'internal_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Status options untuk tiket
     */
    public const STATUS_OPTIONS = [
        'open' => 'Terbuka',
        'in_progress' => 'Sedang Diproses',
        'resolved' => 'Terselesaikan',
        'closed' => 'Ditutup',
    ];

    /**
     * Priority options untuk tiket
     */
    public const PRIORITY_OPTIONS = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
        'urgent' => 'Mendesak',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Generate kode tiket otomatis saat membuat tiket baru
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->kode_tiket)) {
                // Format: TKT-YYYYMMDD-XXXX
                $prefix = 'TKT-' . now()->format('Ymd');
                
                // Cari tiket terakhir hari ini untuk mendapatkan nomor urut berikutnya
                $lastTicket = self::where('kode_tiket', 'like', $prefix . '%')->latest('id')->first();
                
                $nextNumber = 1;
                if ($lastTicket) {
                    $lastNumber = (int) substr($lastTicket->kode_tiket, -4);
                    $nextNumber = $lastNumber + 1;
                }

                $ticket->kode_tiket = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // Auto-set layanan_id dari permohonan jika permohonan_id ada
            if ($ticket->permohonan_id && !$ticket->layanan_id) {
                $permohonan = \App\Models\Permohonan::find($ticket->permohonan_id);
                if ($permohonan) {
                    $ticket->layanan_id = $permohonan->layanan_id;
                }
            }
        });

        // Log perubahan status untuk audit trail
        static::updated(function (Ticket $ticket) {
            if ($ticket->wasChanged('status')) {
                \Log::info('Status tiket berubah', [
                    'kode_tiket' => $ticket->kode_tiket,
                    'status_lama' => $ticket->getOriginal('status'),
                    'status_baru' => $ticket->status,
                    'updated_by' => auth()->user()?->name ?? 'System',
                ]);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'kode_tiket';
    }

    /**
     * Relasi ke permohonan
     */
    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    /**
     * Relasi ke layanan (jenis layanan)
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    /**
     * Relasi ke user yang membuat tiket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke petugas yang menangani tiket
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relasi ke pesan-pesan dalam tiket
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Mendapatkan warna badge untuk status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'gray',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'closed' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Mendapatkan warna badge untuk prioritas
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
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
     * Mendapatkan label prioritas yang user-friendly
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITY_OPTIONS[$this->priority] ?? $this->priority;
    }

    /**
     * Scope untuk tiket yang terbuka
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Scope untuk tiket yang sudah selesai
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope untuk tiket berdasarkan layanan
     */
    public function scopeByLayanan($query, $layananId)
    {
        return $query->where('layanan_id', $layananId);
    }

    /**
     * Scope untuk tiket berdasarkan prioritas
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Cek apakah tiket masih aktif
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }

    /**
     * Mendapatkan pesan terakhir
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Menghitung jumlah pesan yang belum dibaca
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mendapatkan kategori tiket untuk display
     */
    public function getCategoryDisplayAttribute(): string
    {
        if ($this->permohonan) {
            return 'Permohonan: ' . $this->permohonan->kode_permohonan;
        } elseif ($this->layanan) {
            return 'Layanan: ' . $this->layanan->name;
        }
        return 'Pertanyaan Umum';
    }

    /**
     * Mendapatkan deskripsi lengkap kategori
     */
    public function getCategoryDescriptionAttribute(): string
    {
        if ($this->permohonan) {
            return "Terkait permohonan {$this->permohonan->kode_permohonan} - {$this->permohonan->layanan->name}";
        } elseif ($this->layanan) {
            return "Pertanyaan umum tentang {$this->layanan->kategoriLayanan->name} - {$this->layanan->name}";
        }
        return 'Pertanyaan umum';
    }

    /**
     * Cek apakah tiket dapat ditutup
     */
    public function canBeClosed(): bool
    {
        return in_array($this->status, ['resolved']);
    }

    /**
     * Cek apakah tiket dapat dibuka kembali
     */
    public function canBeReopened(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Mendapatkan durasi resolusi tiket
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if ($this->resolved_at && $this->created_at) {
            return $this->created_at->diffInHours($this->resolved_at);
        }
        return null;
    }
}