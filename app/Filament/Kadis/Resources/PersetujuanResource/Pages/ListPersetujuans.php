<?php

namespace App\Filament\Kadis\Resources\PersetujuanResource\Pages;

use App\Filament\Kadis\Resources\PersetujuanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListPersetujuans extends ListRecords
{
    protected static string $resource = PersetujuanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode Permohonan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Warga')->searchable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Jenis Layanan')->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->label('Masuk Antrean')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => PersetujuanResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Tidak ada permohonan yang menunggu persetujuan.');
    }
}