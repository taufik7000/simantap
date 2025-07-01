<?php

namespace App\Filament\Kadis\Resources\KategoriLayananResource\Pages;

use App\Filament\Kadis\Resources\KategoriLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;



class ListKategoriLayanans extends ListRecords
{
    protected static string $resource = KategoriLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
