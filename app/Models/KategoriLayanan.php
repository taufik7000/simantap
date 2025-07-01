<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriLayanan extends Model
{
    use HasFactory;
    protected $table = 'kategori_layanan';
    protected $fillable = ['name', 'description', 'icon'];

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }
}