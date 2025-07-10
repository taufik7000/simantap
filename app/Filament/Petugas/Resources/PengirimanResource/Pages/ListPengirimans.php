<?php

namespace App\Filament\Petugas\Resources\PengirimanResource\Pages;

use App\Filament\Petugas\Resources\PengirimanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListPengirimans extends ListRecords
{
    protected static string $resource = PengirimanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode Permohonan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Warga')->searchable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Jenis Layanan')->wrap(),
                Tables\Columns\TextColumn::make('dokumen_diterbitkan_at')->label('Diterbitkan pada')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => PengirimanResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Tidak ada dokumen yang menunggu untuk dikirim.');
    }
}