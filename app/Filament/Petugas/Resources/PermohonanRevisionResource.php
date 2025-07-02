<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PermohonanRevisionResource\Pages;
use App\Models\PermohonanRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class PermohonanRevisionResource extends Resource
{
    protected static ?string $model = PermohonanRevision::class;
    protected static ?string $navigationGroup = 'Manajemen Pelayanan';
    protected static ?string $navigationLabel = 'Revisi Permohonan';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Revisi')
                            ->schema([
                                Forms\Components\TextInput::make('permohonan.kode_permohonan')
                                    ->label('Kode Permohonan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('user.name')
                                    ->label('Warga')
                                    ->disabled(),
                                Forms\Components\TextInput::make('revision_number')
                                    ->label('Revisi Ke')
                                    ->disabled(),
                                Forms\Components\Textarea::make('catatan_revisi')
                                    ->label('Catatan dari Warga')
                                    ->disabled()
                                    ->rows(3),
                            ])->columns(2),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Review Petugas')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status Review')
                                    ->options(PermohonanRevision::STATUS_OPTIONS)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $defaultMessages = [
                                            'reviewed' => 'Revisi telah ditinjau.',
                                            'accepted' => 'Revisi diterima dan akan diproses lebih lanjut.',
                                            'rejected' => 'Revisi ditolak. Silakan perbaiki dokumen sesuai catatan.',
                                        ];

                                        if (isset($defaultMessages[$state])) {
                                            $set('catatan_petugas', $defaultMessages[$state]);
                                        }
                                    }),

                                Forms\Components\Textarea::make('catatan_petugas')
                                    ->label('Catatan Review')
                                    ->required(fn (Forms\Get $get) => in_array($get('status'), ['rejected']))
                                    ->rows(4)
                                    ->placeholder('Berikan feedback untuk warga...'),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permohonan.kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Warga')
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

                Tables\Columns\TextColumn::make('reviewedBy.name')
                    ->label('Direview Oleh')
                    ->default('Belum direview'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),
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
                        TextEntry::make('user.name')->label('Warga'),
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
                            ->label('Catatan dari Warga')
                            ->markdown()
                            ->columnSpanFull(),
                        TextEntry::make('catatan_petugas')
                            ->label('Catatan Review')
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

                // Informasi Permohonan Asli
                InfolistSection::make('Detail Permohonan Asli')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('permohonan.layanan.name')->label('Jenis Layanan'),
                        TextEntry::make('permohonan.status')->label('Status Permohonan')->badge(),
                        TextEntry::make('permohonan.data_pemohon.jenis_permohonan')->label('Jenis Permohonan'),
                        TextEntry::make('permohonan.catatan_petugas')->label('Catatan Permohonan')->markdown()->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanRevisions::route('/'),
            'view' => Pages\ViewPermohonanRevision::route('/{record}'),
            'edit' => Pages\EditPermohonanRevision::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getNavigationBadge();
        return $count > 0 ? 'warning' : null;
    }
}