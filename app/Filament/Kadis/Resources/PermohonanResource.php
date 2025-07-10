<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\PermohonanResource\Pages;
use App\Models\Permohonan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Permohonan';
    protected static ?string $navigationLabel = 'Semua Permohonan';
    protected static ?string $modelLabel = 'Permohonan';
    protected static ?string $pluralModelLabel = 'Semua Permohonan';

    public static function getEloquentQuery(): Builder
    {
        // Kadis bisa melihat semua permohonan
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        $petugasForm = \App\Filament\Petugas\Resources\PermohonanResource::form($form);
        return $petugasForm->disabled(); // Membuat seluruh form tidak bisa diedit
    }

    public static function table(Table $table): Table
    {
        // Menggunakan definisi tabel dari PetugasResource agar konsisten
        return \App\Filament\Petugas\Resources\PermohonanResource::table($table)
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tombol edit bisa disembunyikan jika Kadis hanya perlu melihat
                Tables\Actions\EditAction::make()->visible(false), 
            ])
            ->bulkActions([]); // Sembunyikan bulk actions untuk Kadis
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'view' => Pages\ViewPermohonan::route('/{record:kode_permohonan}'),
        ];
    }

    public static function canCreate(): bool
    {
        // Kadis tidak membuat permohonan
        return false;
    }
}