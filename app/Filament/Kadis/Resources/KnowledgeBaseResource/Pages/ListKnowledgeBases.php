<?php

namespace App\Filament\Kadis\Resources\KnowledgeBaseResource\Pages;

use App\Filament\Kadis\Resources\KnowledgeBaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgeBases extends ListRecords
{
    protected static string $resource = KnowledgeBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
