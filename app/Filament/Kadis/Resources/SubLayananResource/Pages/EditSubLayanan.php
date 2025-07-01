<?php

namespace App\Filament\Kadis\Resources\SubLayananResource\Pages;

use App\Filament\Kadis\Resources\SubLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubLayanan extends EditRecord
{
    protected static string $resource = SubLayananResource::class;
    protected static ?string $title = 'Edit Layanan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
