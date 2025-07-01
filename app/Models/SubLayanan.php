<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubLayanan extends Model
{
    use HasFactory;
    
    // Perbarui fillable
    protected $fillable = ['layanan_id', 'name', 'slug', 'description', 'formulir_master_id', 'is_active', 'icon'];

    // Perbarui casts
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }
    
    // Relasi baru ke FormulirMaster
    public function formulirMaster(): BelongsTo
    {
        return $this->belongsTo(FormulirMaster::class);
    }
}