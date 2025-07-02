<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\PermohonanRevisionResource\Pages;
use App\Models\PermohonanRevision;
use App\Models\Permohonan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PermohonanRevisionResource extends Resource
{
    protected static ?string $model = PermohonanRevision::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Revisi Permohonan';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('permohonan_id')
                    ->label('Pilih Permohonan untuk Direvisi')
                    ->options(function () {
                        return Permohonan::where('user_id', Auth::id())
                            ->whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan'])
                            ->where(function ($query) {
                                // Hanya permohonan yang belum ada revisi pending
                                $query->whereDoesntHave('revisions', function ($q) {
                                    $q->where('status', 'pending');
                                });
                            })
                            ->with('layanan')
                            ->get()
                            ->mapWithKeys(function ($permohonan) {
                                $jenisPermohonan = $permohonan->data_pemohon['jenis_permohonan'] ?? 'Tidak ada jenis permohonan';
                                return [
                                    $permohonan->id => $permohonan->kode_permohonan . ' - ' . $jenisPermohonan
                                ];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, $set) => $state ? null : $set('berkas_revisi', [])),

                Forms\Components\Textarea::make('catatan_revisi')
                    ->label('Catatan Revisi')
                    ->placeholder('Jelaskan perubahan yang Anda lakukan...')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Repeater::make('berkas_revisi')
                    ->label('Dokumen Revisi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_dokumen')
                            ->label('Nama Dokumen')
                            ->required(),

                        Forms\Components\FileUpload::make('path_dokumen')
                            ->label('File Revisi')
                            ->disk('private')
                            ->directory('berkas-revisi')
                            ->required(),
                    ])
                    ->addActionLabel('Tambah Dokumen')
                    ->columnSpanFull()
                    ->minItems(1)
                    ->visible(fn ($get) => !empty($get('permohonan_id'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permohonan.kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('revision_number')
                    ->label('Revisi Ke')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => PermohonanRevision::STATUS_OPTIONS[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Kirim')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Tanggal Review')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Revisi')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('permohonan.kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('revision_number')->label('Revisi Ke')->badge(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'reviewed' => 'info', 
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => PermohonanRevision::STATUS_OPTIONS[$state] ?? $state),
                        TextEntry::make('created_at')->label('Tanggal Kirim')->dateTime(),
                        TextEntry::make('reviewed_at')->label('Tanggal Review')->dateTime(),
                        TextEntry::make('reviewedBy.name')->label('Direview Oleh'),
                    ]),

                InfolistSection::make('Catatan')
                    ->schema([
                        TextEntry::make('catatan_revisi')
                            ->label('Catatan Anda')
                            ->markdown()
                            ->columnSpanFull(),
                        TextEntry::make('catatan_petugas')
                            ->label('Feedback Petugas')
                            ->markdown()
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->catatan_petugas)),
                    ]),

                InfolistSection::make('Dokumen Revisi')
                    ->schema(function (PermohonanRevision $record) {
                        $berkasFields = [];
                        if (is_array($record->berkas_revisi)) {
                            foreach ($record->berkas_revisi as $index => $berkas) {
                                if (empty($berkas['path_dokumen'])) continue;
                                $berkasFields[] = TextEntry::make("berkas_revisi.{$index}.nama_dokumen")
                                    ->label('Nama Dokumen')
                                    ->url(fn() => route('secure.download.revision', [
                                        'revision_id' => $record->id,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanRevisions::route('/'),
            'create' => Pages\CreatePermohonanRevision::route('/create'),
            'view' => Pages\ViewPermohonanRevision::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        // Hanya bisa create jika ada permohonan yang perlu revisi
        return Permohonan::where('user_id', Auth::id())
            ->whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan'])
            ->where(function ($query) {
                $query->whereDoesntHave('revisions', function ($q) {
                    $q->where('status', 'pending');
                });
            })
            ->exists();
    }
}