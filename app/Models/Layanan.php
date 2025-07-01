<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layanan extends Model
{
    use HasFactory;
    protected $table = 'layanan';
    protected $fillable = ['kategori_layanan_id', 'name', 'slug', 'description', 'is_active', 'icon'];
    protected $casts = [
        'is_active' => 'boolean',
        'description' => 'array',
    ];

    public function kategoriLayanan(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class);
    }

    public function formulirMaster(): BelongsTo
    {
        return $this->belongsTo(FormulirMaster::class);
    }
}