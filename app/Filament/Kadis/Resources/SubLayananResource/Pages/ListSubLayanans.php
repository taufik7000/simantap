<?php

namespace App\Filament\Kadis\Resources\SubLayananResource\Pages;

use App\Filament\Kadis\Resources\SubLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubLayanans extends ListRecords
{
    protected static string $resource = SubLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
