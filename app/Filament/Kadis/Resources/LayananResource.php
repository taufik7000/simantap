<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\LayananResource\Pages;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Layanan';
    protected static ?string $navigationGroup = 'Manajemen Layanan';
    protected static ?string $pluralModelLabel = 'Kategori Layanan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori Layanan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Singkat')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Kategori')->searchable(),
                Tables\Columns\TextColumn::make('sub_layanans_count')->counts('subLayanans')->label('Jumlah Sub Layanan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        // Kosongkan ini untuk menghapus Relation Manager
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }    
}