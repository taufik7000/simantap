<?php

namespace App\Filament\Kadis\Resources\KategoriLayananResource\Pages;

use App\Filament\Kadis\Resources\KategoriLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriLayanan extends EditRecord
{
    protected static string $resource = KategoriLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
