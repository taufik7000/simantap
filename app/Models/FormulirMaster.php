<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulirMaster extends Model
{
    use HasFactory;
    protected $fillable = ['nama_formulir', 'file_path'];
}