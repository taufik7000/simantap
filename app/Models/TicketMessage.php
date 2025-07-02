<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_internal',
        'read_at',
        'message_type',
        'priority',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Message types
     */
    public const MESSAGE_TYPES = [
        'message' => 'Pesan Biasa',
        'status_update' => 'Update Status',
        'system' => 'Pesan Sistem',
        'escalation' => 'Eskalasi',
        'resolution' => 'Penyelesaian',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Auto-update ticket's updated_at when message is created
        static::created(function (TicketMessage $message) {
            $message->ticket->touch();
            
            // Update ticket status jika pesan dari user dan tiket sudah resolved
            if (!$message->is_internal && 
                $message->user_id === $message->ticket->user_id && 
                $message->ticket->status === 'resolved') {
                $message->ticket->update(['status' => 'in_progress']);
            }
        });

        // Log activity for audit trail
        static::created(function (TicketMessage $message) {
            \Log::info('Pesan tiket baru', [
                'ticket_id' => $message->ticket_id,
                'kode_tiket' => $message->ticket->kode_tiket,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'is_internal' => $message->is_internal,
                'message_type' => $message->message_type ?? 'message',
            ]);
        });
    }

    /**
     * Relasi ke tiket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relasi ke user yang mengirim pesan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tandai pesan sebagai sudah dibaca
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Cek apakah pesan sudah dibaca
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Cek apakah pesan dari petugas
     */
    public function isFromStaff(): bool
    {
        return $this->user->hasAnyRole(['petugas', 'admin', 'kadis']);
    }

    /**
     * Cek apakah pesan dari pemohon
     */
    public function isFromCustomer(): bool
    {
        return $this->user->hasRole('warga');
    }

    /**
     * Mendapatkan tipe pengirim
     */
    public function getSenderTypeAttribute(): string
    {
        if ($this->isFromStaff()) {
            return 'staff';
        } elseif ($this->isFromCustomer()) {
            return 'customer';
        }
        return 'unknown';
    }

    /**
     * Mendapatkan label tipe pesan
     */
    public function getMessageTypeLabelAttribute(): string
    {
        return self::MESSAGE_TYPES[$this->message_type ?? 'message'] ?? 'Pesan Biasa';
    }

    /**
     * Cek apakah pesan memiliki attachment
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) && is_array($this->attachments);
    }

    /**
     * Mendapatkan jumlah attachment
     */
    public function getAttachmentCountAttribute(): int
    {
        return $this->hasAttachments() ? count($this->attachments) : 0;
    }

    /**
     * Mendapatkan ukuran pesan (karakter)
     */
    public function getMessageLengthAttribute(): int
    {
        return strlen($this->message);
    }

    /**
     * Cek apakah pesan adalah pesan panjang
     */
    public function isLongMessage(): bool
    {
        return $this->message_length > 500;
    }

    /**
     * Mendapatkan preview pesan (untuk list view)
     */
    public function getMessagePreviewAttribute(): string
    {
        $maxLength = 100;
        if (strlen($this->message) <= $maxLength) {
            return $this->message;
        }
        
        return substr($this->message, 0, $maxLength) . '...';
    }

    /**
     * Scope untuk pesan yang bukan internal
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope untuk pesan internal
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope untuk pesan yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope untuk pesan yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope untuk pesan dari user tertentu
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk pesan dengan attachment
     */
    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachments')
            ->where('attachments', '!=', '[]');
    }

    /**
     * Scope untuk pesan berdasarkan tipe
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope untuk pesan dari petugas
     */
    public function scopeFromStaff($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->role(['petugas', 'admin', 'kadis']);
        });
    }

    /**
     * Scope untuk pesan dari warga
     */
    public function scopeFromCustomers($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->role('warga');
        });
    }

    /**
     * Mendapatkan waktu response (jika ini balasan)
     */
    public function getResponseTimeAttribute(): ?int
    {
        // Cari pesan sebelumnya dari user yang berbeda
        $previousMessage = TicketMessage::where('ticket_id', $this->ticket_id)
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '<', $this->created_at)
            ->latest()
            ->first();

        if ($previousMessage) {
            return $previousMessage->created_at->diffInMinutes($this->created_at);
        }

        return null;
    }

    /**
     * Cek apakah ini pesan pertama dalam tiket
     */
    public function isFirstMessage(): bool
    {
        return $this->ticket->messages()->orderBy('created_at')->first()?->id === $this->id;
    }

    /**
     * Cek apakah ini pesan terakhir dalam tiket
     */
    public function isLastMessage(): bool
    {
        return $this->ticket->messages()->latest()->first()?->id === $this->id;
    }

    /**
     * Mendapatkan posisi pesan dalam urutan (1-based)
     */
    public function getPositionAttribute(): int
    {
        return TicketMessage::where('ticket_id', $this->ticket_id)
            ->where('created_at', '<=', $this->created_at)
            ->count();
    }

    /**
     * Format attachment untuk display
     */
    public function getFormattedAttachmentsAttribute(): array
    {
        if (!$this->hasAttachments()) {
            return [];
        }

        return collect($this->attachments)->map(function ($attachment) {
            $filename = basename($attachment);
            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
            
            return [
                'filename' => $filename,
                'path' => $attachment,
                'extension' => $extension,
                'is_image' => in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']),
                'size' => file_exists(storage_path('app/private/' . $attachment)) 
                    ? filesize(storage_path('app/private/' . $attachment)) 
                    : null,
            ];
        })->toArray();
    }

    /**
     * Mendapatkan sentiment pesan (placeholder untuk AI analysis)
     */
    public function getSentimentAttribute(): ?string
    {
        // Placeholder untuk analisis sentiment AI di masa depan
        // Bisa menggunakan library seperti TextBlob atau API sentiment analysis
        return null;
    }
}