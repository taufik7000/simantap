<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\PermohonanResource\Pages;
use App\Models\Permohonan;
use App\Models\SubLayanan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater as FormRepeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationLabel = 'Status Permohonan';
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        
        $subLayanan = null;
        
        if ($form->getLivewire() instanceof Pages\CreatePermohonan) {
            $subLayanan = $form->getLivewire()->subLayanan;
        } else {
            $record = $form->getRecord();
            if ($record) {
                $subLayanan = $record->subLayanan;
            }
        }
        
        if (!$subLayanan) {
            return $form->schema([]);
        }
        $jenisPermohonanOptions = [];
        $deskripsiLengkap = [];

        if ($subLayanan->description && is_array($subLayanan->description)) {
            foreach ($subLayanan->description as $index => $syarat) {
                $jenisPermohonanOptions[$syarat['nama_syarat']] = $syarat['nama_syarat'];
                $deskripsiLengkap[$syarat['nama_syarat']] = $syarat['deskripsi_syarat'];
            }
        }

        return $form
            ->schema([
                Section::make('Pilih Jenis Permohonan')
                    ->schema([
                        Select::make('data_pemohon.jenis_permohonan')
                            ->label('Pilih Jenis Permohonan yang Diajukan')
                            ->options($jenisPermohonanOptions)
                            ->required()
                            ->searchable()
                            ->reactive(),
                        Placeholder::make('deskripsi_placeholder')
                            ->label('Deskripsi dan Persyaratan')
                            ->content(function ($get) use ($deskripsiLengkap) {
                                $selected = $get('data_pemohon.jenis_permohonan');
                                if (!$selected) {
                                    return 'Pilih jenis permohonan terlebih dahulu untuk melihat deskripsi.';
                                }
                                return new HtmlString($deskripsiLengkap[$selected] ?? 'Deskripsi tidak ditemukan.');
                            })
                            ->hidden(fn ($get) => !$get('data_pemohon.jenis_permohonan')),
                    ]),
                Section::make('Unggah Dokumen Pendukung')
                    ->description('Unggah semua dokumen yang diperlukan berdasarkan deskripsi di atas.')
                    ->schema([
                        FormRepeater::make('berkas_pemohon')
                            ->label('Dokumen yang Diunggah')
                            ->addActionLabel('Tambah Dokumen')
                            ->schema([
                                TextInput::make('nama_dokumen')
                                    ->label('Nama atau Keterangan Dokumen')
                                    ->placeholder('Contoh: Scan Kartu Keluarga Asli')
                                    ->required(),
                                FileUpload::make('path_dokumen')
                                    ->label('Pilih File')
                                    ->disk('private') 
                                    ->directory('berkas-permohonan')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->required(),
                    ])
                    ->hidden(fn ($get) => !$get('data_pemohon.jenis_permohonan')),
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
                        TextEntry::make('subLayanan.name')->label('Kategori Layanan'),
                        TextEntry::make('data_pemohon.jenis_permohonan')->label('Jenis Permohonan'),
                        
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
                    ]),
                
                InfolistSection::make('Berkas Terlampir')
                    ->schema(function (Permohonan $record) {
                        $berkasFields = [];
                        if (is_array($record->berkas_pemohon)) {
                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                if (empty($berkas['path_dokumen'])) continue;
                                $berkasFields[] = TextEntry::make("berkas_pemohon.{$index}.nama_dokumen")
                                    ->label('Nama Dokumen')
                                     ->url(fn() => route('secure.download', [
                                        'permohonan_id' => $record->id,
                                        'path' => $berkas['path_dokumen']
                                    ]), true)
                                    ->formatStateUsing(fn() => $berkas['nama_dokumen'] . ' (Unduh)')
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
            'view' => Pages\ViewPermohonan::route('/{record:kode_permohonan}'),
        ];
    }
}