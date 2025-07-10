<?php

namespace App\Filament\Petugas\Resources\PenerbitanResource\Pages;

use App\Filament\Petugas\Resources\PenerbitanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListPenerbitans extends ListRecords
{
    protected static string $resource = PenerbitanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode Permohonan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Warga')->searchable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Jenis Layanan')->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->label('Disetujui pada')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => PenerbitanResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Tidak ada dokumen yang menunggu untuk diterbitkan.');
    }
}