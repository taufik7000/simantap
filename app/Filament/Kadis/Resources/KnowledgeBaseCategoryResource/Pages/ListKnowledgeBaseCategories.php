<?php

namespace App\Filament\Kadis\Resources\KnowledgeBaseCategoryResource\Pages;

use App\Filament\Kadis\Resources\KnowledgeBaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

// Pastikan nama kelas ini benar: ListKnowledgeBaseCategories
class ListKnowledgeBaseCategories extends ListRecords
{
    // Pastikan resource ini menunjuk ke yang benar: KnowledgeBaseCategoryResource
    protected static string $resource = KnowledgeBaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}