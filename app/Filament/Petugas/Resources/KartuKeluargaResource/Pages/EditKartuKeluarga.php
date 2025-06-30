<?php

namespace App\Filament\Petugas\Resources\KartuKeluargaResource\Pages;

use App\Filament\Petugas\Resources\KartuKeluargaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKartuKeluarga extends EditRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}