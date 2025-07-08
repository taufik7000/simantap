<?php

namespace App\Filament\Petugas\Resources\EntriDataResource\Pages;

use App\Filament\Petugas\Resources\EntriDataResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListEntriData extends ListRecords
{
    protected static string $resource = EntriDataResource::class;

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
                Tables\Actions\ViewAction::make()->url(fn ($record) => EntriDataResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Tidak ada data untuk di-entri');
    }
}