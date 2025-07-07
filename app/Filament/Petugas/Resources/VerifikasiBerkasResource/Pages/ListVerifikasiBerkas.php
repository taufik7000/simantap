<?php

namespace App\Filament\Petugas\Resources\VerifikasiBerkasResource\Pages;

use App\Filament\Petugas\Resources\VerifikasiBerkasResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListVerifikasiBerkas extends ListRecords
{
    protected static string $resource = VerifikasiBerkasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode Permohonan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Warga')->searchable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Jenis Layanan')->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->label('Tugas Diterima')->since()->sortable(),
            ])
            ->actions([
                // Arahkan tombol view ke halaman view kustom kita
                Tables\Actions\ViewAction::make()->url(fn ($record): string => 
                    VerifikasiBerkasResource::getUrl('view', ['record' => $record])
                ),
            ])
            ->emptyStateHeading('Tidak ada berkas untuk diverifikasi')
            ->emptyStateDescription('Semua tugas verifikasi sudah selesai. Kerja bagus!');
    }
}