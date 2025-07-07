<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\RelationManagers;

use App\Models\PermohonanRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';
    protected static ?string $recordTitleAttribute = 'revision_number';
    protected static ?string $title = 'Riwayat Revisi dari Warga';
    protected static ?string $modelLabel = 'Revisi';
    protected static ?string $pluralModelLabel = 'Revisi';

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
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('catatan_revisi')
                    ->label('Catatan dari Warga')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'pending' => 'Menunggu Review',
                            'reviewed' => 'Sudah Direview',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            default => 'Belum direview'
                        };
                    })
                    ->color(function ($state) {
                        return match($state) {
                            'pending' => 'warning',
                            'reviewed' => 'info',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            default => 'gray'
                        };
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('berkas_revisi')
                    ->label('Berkas')
                    ->formatStateUsing(fn ($state) => $state ? count($state) . ' file' : '0 file')
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
            ->actions([
                // Tombol View Detail
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye'),

                // Tombol Edit/Review (hanya untuk status pending)
                Tables\Actions\EditAction::make()
                    ->label('Review')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending'),

                // Tombol TERIMA (hanya untuk status pending)
                Tables\Actions\Action::make('quick_approve')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Revisi')
                    ->modalDescription(function ($record) {
                        return "Apakah Anda yakin ingin menerima revisi ke-{$record->revision_number}?";
                    })
                    ->action(function (PermohonanRevision $record) {
                        $record->update([
                            'status' => 'accepted',
                            'catatan_petugas' => 'Revisi diterima dan akan diproses lebih lanjut.',
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);

                        // Update status permohonan induk
                        $record->permohonan->update([
                            'status' => 'verifikasi_berkas',
                            'catatan_petugas' => "Revisi ke-{$record->revision_number} diterima. Permohonan akan diproses lebih lanjut.",
                        ]);

                        // Kirim notifikasi ke warga
                        Notification::make()
                            ->title('Revisi Anda Telah Diterima')
                            ->body("Revisi ke-{$record->revision_number} telah diterima dan akan diproses lebih lanjut.")
                            ->success()
                            ->sendToDatabase($record->user);

                        // Notifikasi untuk petugas
                        Notification::make()
                            ->title('Revisi Diterima')
                            ->body("Revisi ke-{$record->revision_number} berhasil diterima.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                // Tombol TOLAK (hanya untuk status pending)
                Tables\Actions\Action::make('quick_reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(4)
                            ->placeholder('Jelaskan mengapa revisi ini ditolak dan apa yang perlu diperbaiki...'),
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
                            ->body("Revisi ke-{$record->revision_number} ditolak. Alasan: " . $data['catatan_petugas'])
                            ->warning()
                            ->sendToDatabase($record->user);

                        // Notifikasi untuk petugas
                        Notification::make()
                            ->title('Revisi Ditolak')
                            ->body("Revisi ke-{$record->revision_number} berhasil ditolak.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

                // Tombol Unduh Berkas
                Tables\Actions\Action::make('download_files')
                    ->label('Unduh Berkas')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn ($record) => !empty($record->berkas_revisi))
                    ->url(function ($record) {
                        // Implementasi download berkas
                        return route('petugas.download-revision-files', $record->id);
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk actions bisa ditambahkan di sini jika diperlukan
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada revisi')
            ->emptyStateDescription('Belum ada revisi yang dikirim oleh warga untuk permohonan ini.');
    }

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
                                $set('catatan_petugas', $defaultMessages[$state] ?? '');
                            }),

                        Forms\Components\Textarea::make('catatan_petugas')
                            ->label('Catatan Petugas')
                            ->required(fn (Forms\Get $get) => $get('status') === 'rejected')
                            ->rows(4)
                            ->placeholder('Tambahkan catatan untuk warga...'),
                    ])->columns(1),
            ]);
    }
}