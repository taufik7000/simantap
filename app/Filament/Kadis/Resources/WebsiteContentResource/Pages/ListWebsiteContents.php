<?php

namespace App\Filament\Kadis\Resources\WebsiteContentResource\Pages;

use App\Filament\Kadis\Resources\WebsiteContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteContents extends ListRecords
{
    protected static string $resource = WebsiteContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
