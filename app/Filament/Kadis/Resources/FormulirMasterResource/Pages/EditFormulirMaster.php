<?php

namespace App\Filament\Kadis\Resources\FormulirMasterResource\Pages;

use App\Filament\Kadis\Resources\FormulirMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormulirMaster extends EditRecord
{
    protected static string $resource = FormulirMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
