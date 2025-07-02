<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermohonan extends EditRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
