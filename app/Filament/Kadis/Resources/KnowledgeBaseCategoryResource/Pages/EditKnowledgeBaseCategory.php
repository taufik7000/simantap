<?php

namespace App\Filament\Kadis\Resources\KnowledgeBaseCategoryResource\Pages;

use App\Filament\Kadis\Resources\KnowledgeBaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeBaseCategory extends EditRecord
{
    protected static string $resource = KnowledgeBaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
