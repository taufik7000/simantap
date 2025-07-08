<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WhatsAppSettings extends Settings
{
    public string $access_token;
    public string $phone_number_id;
    public string $app_id;
    public string $app_secret;
    public string $otp_template_name;

    public static function group(): string
    {
        return 'whatsapp';
    }
}