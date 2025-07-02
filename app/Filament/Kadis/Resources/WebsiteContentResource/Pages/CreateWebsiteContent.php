<?php

namespace App\Filament\Kadis\Resources\WebsiteContentResource\Pages;

use App\Filament\Kadis\Resources\WebsiteContentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWebsiteContent extends CreateRecord
{
    protected static string $resource = WebsiteContentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}