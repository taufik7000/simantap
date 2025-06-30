<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function subLayanans(): HasMany
    {
        return $this->hasMany(SubLayanan::class);
    }
}