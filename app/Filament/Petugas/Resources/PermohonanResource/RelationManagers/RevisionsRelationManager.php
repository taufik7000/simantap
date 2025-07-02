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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';
    protected static ?string $recordTitleAttribute = 'revision_number';
    protected static ?string $title = 'Riwayat Revisi';
    protected static ?string $modelLabel = 'Revisi';
    protected static ?string $pluralModelLabel = 'Revisi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Revisi')
                    ->schema([
                        Forms\Components\TextInput::make('revision_number')
                            ->label('Revisi Ke')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Dikirim Oleh')
                            ->disabled(),
                        Forms\Components\Textarea::make('catatan_revisi')
                            ->label('Catatan dari Warga')
                            ->disabled()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Review Petugas')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Review')
                            ->options([
                                'pending' => 'Menunggu Review',
                                'reviewed' => 'Sudah Direview',
                                'accepted' => 'Diterima',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $defaultMessages = [
                                    'reviewed' => 'Revisi telah ditinjau oleh petugas.',
                                    'accepted' => 'Revisi diterima dan akan diproses lebih lanjut.',
                                    'rejected' => 'Revisi ditolak. Silakan perbaiki dokumen sesuai catatan.',
                                ];

                                if (isset($defaultMessages[$state])) {
                                    $set('catatan_petugas', $defaultMessages[$state]);
                                }
                            }),

                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Catatan Review')
                            ->required(fn (Forms\Get $get) => $get('status') === 'rejected')
                            ->rows(4)
                            ->placeholder('Berikan feedback untuk warga...'),
                    ]),
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
                    ->color('primary')
                    ->sortable(),

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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu Review',
                        'reviewed' => 'Sudah Direview',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Kirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Tanggal Review')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewedBy.name')
                    ->label('Direview Oleh')
                    ->default('Belum direview')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('berkas_count')
                    ->label('Jumlah Berkas')
                    ->state(fn ($record) => is_array($record->berkas_revisi) ? count($record->berkas_revisi) : 0)
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Review',
                        'reviewed' => 'Sudah Direview',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ])
                    ->native(false),
            ])
            ->headerActions([
                // Tidak ada create action karena revisi dibuat oleh warga
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),

                Tables\Actions\EditAction::make()
                    ->label('Review')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('quick_approve')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Revisi')
                    ->modalDescription('Apakah Anda yakin ingin menerima revisi ini?')
                    ->action(function (PermohonanRevision $record) {
                        $record->update([
                            'status' => 'accepted',
                            'catatan_petugas' => 'Revisi diterima dan akan diproses lebih lanjut.',
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);

                        // Update status permohonan induk
                        $record->permohonan->update([
                            'status' => 'diproses',
                            'catatan_petugas' => "Revisi ke-{$record->revision_number} diterima. Permohonan akan diproses lebih lanjut.",
                        ]);

                        // Kirim notifikasi ke warga
                        Notification::make()
                            ->title('Revisi Anda Telah Diterima')
                            ->body("Revisi ke-{$record->revision_number} telah diterima dan akan diproses lebih lanjut.")
                            ->success()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Revisi Diterima')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('quick_reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3)
                            ->placeholder('Jelaskan mengapa revisi ini ditolak...'),
                    ])
                    ->action(function (PermohonanRevision $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'catatan_petugas' => $data['catatan_petugas'],
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);

                        // Update status permohonan induk
                        $record->permohonan->update([
                            'status' => 'membutuhkan_revisi',
                            'catatan_petugas' => "Revisi ke-{$record->revision_number} ditolak. " . $data['catatan_petugas'],
                        ]);

                        // Kirim notifikasi ke warga
                        Notification::make()
                            ->title('Revisi Perlu Diperbaiki')
                            ->body("Revisi ke-{$record->revision_number} ditolak. " . $data['catatan_petugas'])
                            ->warning()
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->title('Revisi Ditolak')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\Action::make('download_files')
                    ->label('Unduh Berkas')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn ($record) => $record->berkas_revisi ? '#' : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->berkas_revisi)),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_review')
                    ->label('Review Massal')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Review')
                            ->options([
                                'reviewed' => 'Sudah Direview',
                                'accepted' => 'Diterima',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Catatan Review')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                        foreach ($records as $record) {
                            $record->update([
                                'status' => $data['status'],
                                'catatan_petugas' => $data['catatan_petugas'],
                                'reviewed_at' => now(),
                                'reviewed_by' => Auth::id(),
                            ]);

                            // Update permohonan status
                            match ($data['status']) {
                                'accepted' => $record->permohonan->update([
                                    'status' => 'diproses',
                                    'catatan_petugas' => "Revisi ke-{$record->revision_number} diterima. " . $data['catatan_petugas'],
                                ]),
                                'rejected' => $record->permohonan->update([
                                    'status' => 'membutuhkan_revisi',
                                    'catatan_petugas' => "Revisi ke-{$record->revision_number} ditolak. " . $data['catatan_petugas'],
                                ]),
                                default => null,
                            };
                        }

                        Notification::make()
                            ->title('Review berhasil untuk ' . $records->count() . ' revisi')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('revision_number', 'desc')
            ->emptyStateHeading('Belum Ada Revisi')
            ->emptyStateDescription('Permohonan ini belum memiliki revisi dokumen.')
            ->emptyStateIcon('heroicon-o-document-text');
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
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Menunggu Review',
                                'reviewed' => 'Sudah Direview',
                                'accepted' => 'Diterima',
                                'rejected' => 'Ditolak',
                                default => $state,
                            }),
                        TextEntry::make('created_at')->label('Tanggal Kirim')->dateTime(),
                        TextEntry::make('reviewed_at')->label('Tanggal Review')->dateTime(),
                        TextEntry::make('reviewedBy.name')->label('Direview Oleh')->default('Belum direview'),
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
                        return $berkasFields ?: [
                            TextEntry::make('no_files')
                                ->label('')
                                ->default('Tidak ada berkas')
                                ->color('gray')
                        ];
                    })->columns(2),
            ]);
    }

    // Override untuk memastikan hanya pending revisions yang bisa diedit
    public function canEdit($record): bool
    {
        return $record->status === 'pending';
    }

    public function canDelete($record): bool
    {
        return false; // Tidak boleh delete untuk audit trail
    }

    public function canCreate(): bool
    {
        return false; // Revisi hanya dibuat oleh warga
    }
}