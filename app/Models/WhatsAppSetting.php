<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSetting extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'access_token',
        'phone_number_id',
        'app_id',
        'app_secret',
        'otp_template_name',
        'verification_enabled',
    ];
}