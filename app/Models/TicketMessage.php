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
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
    ];

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
}