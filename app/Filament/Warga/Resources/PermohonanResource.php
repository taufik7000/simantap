<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\PermohonanResource\Pages;
use App\Models\Permohonan;
use App\Models\SubLayanan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Permohonan Saya';
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        // Dapatkan instance dari halaman CreatePermohonan
        /** @var \App\Filament\Warga\Resources\PermohonanResource\Pages\CreatePermohonan $livewire */
        $livewire = $form->getLivewire();

        // Ambil SubLayanan yang sudah kita muat di halaman Create
        $subLayanan = $livewire->subLayanan;

        // Siapkan array kosong untuk menampung field-field dinamis
        $formulirFields = [];
        $berkasFields = [];

        // --- Membangun Field untuk Isian Data ---
        if ($subLayanan->formulirMaster && !empty($subLayanan->formulirMaster->fields)) {
            foreach ($subLayanan->formulirMaster->fields as $field) {
                $fieldName = 'data_pemohon.' . $field['name']; // Simpan di dalam JSON 'data_pemohon'
                
                // Buat field berdasarkan tipenya
                $formulirFields[] = match ($field['type']) {
                    'select' => Select::make($fieldName)
                                    ->label($field['label'])
                                    ->options($field['options'] ?? [])
                                    ->required($field['is_required'] ?? false),
                    default => TextInput::make($fieldName)
                                    ->label($field['label'])
                                    ->required($field['is_required'] ?? false),
                };
            }
        }

        // --- Membangun Field untuk Unggah Berkas ---
        if ($subLayanan->description && is_array($subLayanan->description)) {
            foreach ($subLayanan->description as $syarat) {
                $fieldName = 'berkas_pemohon.' . \Illuminate\Support\Str::slug($syarat['nama_syarat']);
                
                $berkasFields[] = FileUpload::make($fieldName)
                                    ->label($syarat['nama_syarat'])
                                    ->helperText($syarat['deskripsi_syarat'])
                                    ->required()
                                    ->disk('public') // Simpan ke storage/app/public
                                    ->directory('berkas-permohonan'); // Di dalam subfolder
            }
        }

        return $form->schema([
            Wizard::make([
                Wizard\Step::make('Isi Formulir')
                    ->icon('heroicon-o-pencil')
                    ->schema($formulirFields),

                Wizard\Step::make('Unggah Berkas')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema($berkasFields),
            ])->columnSpanFull()
        ]);
    }

      public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('subLayanan.name')->label('Jenis Layanan'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru' => 'gray',
                                'diproses' => 'warning',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')->label('Tanggal Diajukan')->dateTime(),
                        TextEntry::make('updated_at')->label('Terakhir Diperbarui')->since(),
                    ]),
                
                InfolistSection::make('Data Pemohon')
                    ->schema(function (Permohonan $record) {
                        $dataPemohonFields = [];
                        if (is_array($record->data_pemohon)) {
                            foreach ($record->data_pemohon as $key => $value) {
                                $dataPemohonFields[] = TextEntry::make('data_pemohon.' . $key)
                                    ->label(ucwords(str_replace('_', ' ', $key)));
                            }
                        }
                        return $dataPemohonFields;
                    })->columns(2),

                InfolistSection::make('Berkas Terlampir')
                    ->schema(function (Permohonan $record) {
                        $berkasFields = [];
                        if (is_array($record->berkas_pemohon)) {
                            foreach ($record->berkas_pemohon as $key => $value) {
                                $berkasFields[] = TextEntry::make('berkas_pemohon.' . $key)
                                    ->label(ucwords(str_replace('_', ' ', $key)))
                                    ->url(fn() => Storage::disk('public')->url($value), true) // Buat link unduh
                                    ->formatStateUsing(fn() => 'Unduh Berkas')
                                    ->icon('heroicon-m-arrow-down-tray');
                            }
                        }
                        return $berkasFields;
                    })->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('subLayanan.name')->label('Nama Layanan'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'baru' => 'gray',
                    'diproses' => 'warning',
                    'disetujui' => 'success',
                    'ditolak' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Diajukan')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'create' => Pages\CreatePermohonan::route('/create'),
        ];
    }
}