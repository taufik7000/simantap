<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\FormulirMasterResource\Pages;
use App\Models\FormulirMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormulirMasterResource extends Resource
{
    protected static ?string $model = FormulirMaster::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Manajemen Layanan';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Template Form';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_formulir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File Formulir (PDF)')
                    ->directory('formulir-master')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_formulir')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormulirMasters::route('/'),
            'create' => Pages\CreateFormulirMaster::route('/create'),
            'edit' => Pages\EditFormulirMaster::route('/{record}/edit'),
        ];
    }    
}
