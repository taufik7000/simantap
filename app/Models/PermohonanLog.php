<?php

namespace App\Models;


use App\Enums\StatusPermohonan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'status',
        'catatan',
        'user_id',
    ];

    // 2. Tambahkan properti $casts
    protected $casts = [
        'status' => StatusPermohonan::class,
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}