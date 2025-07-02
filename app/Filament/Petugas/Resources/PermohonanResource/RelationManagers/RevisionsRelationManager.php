<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\RelationManagers;

use App\Models\PermohonanRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';
    protected static ?string $recordTitleAttribute = 'revision_number';
    protected static ?string $title = 'Riwayat Revisi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('revision_number')
                    ->label('Revisi Ke')
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->label('Status Review')
                    ->options(PermohonanRevision::STATUS_OPTIONS)
                    ->required()
                    ->native(false),

                Forms\Components\Textarea::make('catatan_petugas')
                    ->label('Catatan Review')
                    ->required(fn (Forms\Get $get) => $get('status') === 'rejected')
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('revision_number')
            ->columns([
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
                    ->dateTime(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Tanggal Review')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('reviewedBy.name')
                    ->label('Direview Oleh')
                    ->default('Belum direview'),
            ])
            ->headerActions([
                // Tidak ada create action karena revisi dibuat oleh warga
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->defaultSort('revision_number', 'desc');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Revisi')
                    ->columns(3)
                    ->schema([
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
            ]);
    }
}