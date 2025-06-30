<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\SubLayananResource\Pages;
use App\Models\SubLayanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class SubLayananResource extends Resource
{
    protected static ?string $model = SubLayanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Detail Layanan';
    protected static ?string $navigationGroup = 'Manajemen Layanan'; // Grupkan di bawah menu yang sama

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Select::make('layanan_id')
                            ->relationship('layanan', 'name')
                            ->label('Pilih Kategori Layanan Induk')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sub Layanan')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Layanan ini Aktif?')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi Lengkap dan Persyaratan (yang tidak perlu diunggah)')
                    ->helperText('Jelaskan alur dan syarat seperti "Membawa KTP Asli" di sini.')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Lampiran')
                    ->schema([
                        Forms\Components\Select::make('formulir_master_id')
                            ->label('Lampirkan Formulir Master (Opsional)')
                            ->relationship('formulirMaster', 'nama_formulir')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama_formulir')->required(),
                                Forms\Components\FileUpload::make('file_path')->directory('formulir-master')->required(),
                            ]),
                        Repeater::make('required_documents')
                            ->label('Dokumen yang Wajib Diunggah Warga')
                            ->schema([
                                TextInput::make('document_name')
                                    ->label('Nama Dokumen')
                                    ->required(),
                            ])
                            ->addActionLabel('Tambah Dokumen Wajib')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Sub Layanan')->searchable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Kategori Induk')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Status')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubLayanans::route('/'),
            'create' => Pages\CreateSubLayanan::route('/create'),
            'edit' => Pages\EditSubLayanan::route('/{record}/edit'),
        ];
    }    
}