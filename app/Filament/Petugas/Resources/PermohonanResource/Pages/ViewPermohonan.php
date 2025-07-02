<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use App\Models\PermohonanRevision;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Actions as InfolistActions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        // Tandai semua pesan dari pemohon sebagai sudah dibaca jika ada
        $this->record->messages()
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action untuk quick status update
            Actions\Action::make('quickStatusUpdate')
                ->label('Update Status')
                ->icon('heroicon-m-pencil-square')
                ->color('warning')
                ->form([
                    Select::make('status')
                        ->label('Status Baru')
                        ->options([
                            'baru' => 'Baru Diajukan',
                            'sedang_ditinjau' => 'Sedang Ditinjau',
                            'verifikasi_berkas' => 'Verifikasi Berkas',
                            'diproses' => 'Sedang Diproses',
                            'membutuhkan_revisi' => 'Membutuhkan Revisi',
                            'butuh_perbaikan' => 'Butuh Perbaikan',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            'selesai' => 'Selesai',
                        ])
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $defaultMessages = [
                                'sedang_ditinjau' => 'Permohonan Anda sedang dalam tahap peninjauan oleh petugas kami.',
                                'verifikasi_berkas' => 'Kami sedang melakukan verifikasi kelengkapan berkas yang Anda ajukan.',
                                'diproses' => 'Permohonan Anda sedang dalam proses penyelesaian.',
                                'membutuhkan_revisi' => 'Permohonan Anda membutuhkan revisi. Silakan periksa keterangan di bawah ini dan lakukan perbaikan yang diperlukan.',
                                'butuh_perbaikan' => 'Dokumen atau data yang Anda ajukan perlu diperbaiki sebelum dapat diproses lebih lanjut. Silakan periksa detail perbaikan yang diperlukan.',
                                'disetujui' => 'Selamat! Permohonan Anda telah disetujui.',
                                'ditolak' => 'Mohon maaf, permohonan Anda tidak dapat disetujui. Alasan: ',
                                'selesai' => 'Permohonan Anda telah selesai diproses. Terima kasih atas kepercayaan Anda.',
                            ];

                            if (isset($defaultMessages[$state])) {
                                $set('catatan_petugas', $defaultMessages[$state]);
                            }
                        }),
                    Textarea::make('catatan_petugas')
                        ->label('Catatan untuk Pemohon')
                        ->required(fn (Forms\Get $get) => 
                            in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak'])
                        )
                        ->helperText(fn (Forms\Get $get) => 
                            in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak']) 
                                ? 'Catatan wajib diisi untuk memberikan penjelasan kepada pemohon.' 
                                : 'Catatan ini akan dikirim sebagai notifikasi kepada pemohon.'
                        )
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    
                    $this->record->update([
                        'status' => $data['status'],
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);

                    $this->sendStatusUpdateNotification($oldStatus, $data['status'], $data['catatan_petugas']);
                    $this->fillForm();

                    Notification::make()
                        ->title('Status berhasil diperbarui')
                        ->body('Notifikasi telah dikirim kepada pemohon.')
                        ->success()
                        ->send();
                })
                ->modalHeading('Update Status Permohonan')
                ->modalDescription('Update status akan mengirim notifikasi kepada pemohon')
                ->modalSubmitActionLabel('Update & Kirim Notifikasi'),

            // Action untuk approve dengan satu klik
            Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Permohonan')
                ->modalDescription('Apakah Anda yakin ingin menyetujui permohonan ini?')
                ->modalSubmitActionLabel('Ya, Setujui')
                ->form([
                    Textarea::make('catatan_petugas')
                        ->label('Catatan Persetujuan')
                        ->default('Selamat! Permohonan Anda telah disetujui dan akan segera diproses lebih lanjut.')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    
                    $this->record->update([
                        'status' => 'disetujui',
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);

                    $this->sendStatusUpdateNotification($oldStatus, 'disetujui', $data['catatan_petugas']);
                    $this->fillForm();

                    Notification::make()
                        ->title('Permohonan berhasil disetujui')
                        ->success()
                        ->send();
                })
                ->visible(fn () => !in_array($this->record->status, ['disetujui', 'selesai', 'ditolak'])),

            Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Permohonan')
                ->modalDescription('Berikan alasan penolakan yang jelas kepada pemohon.')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->form([
                    Textarea::make('catatan_petugas')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->placeholder('Jelaskan alasan mengapa permohonan ditolak...')
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    
                    $this->record->update([
                        'status' => 'ditolak',
                        'catatan_petugas' => 'Mohon maaf, permohonan Anda tidak dapat disetujui. Alasan: ' . $data['catatan_petugas'],
                    ]);

                    $this->sendStatusUpdateNotification($oldStatus, 'ditolak', $this->record->catatan_petugas);
                    $this->fillForm();

                    Notification::make()
                        ->title('Permohonan berhasil ditolak')
                        ->success()
                        ->send();
                })
                ->visible(fn () => !in_array($this->record->status, ['disetujui', 'selesai', 'ditolak'])),

            // Action untuk minta revisi
            Actions\Action::make('request_revision')
                ->label('Minta Revisi')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->form([
                    Textarea::make('catatan_petugas')
                        ->label('Catatan Revisi')
                        ->required()
                        ->placeholder('Jelaskan dokumen apa yang perlu direvisi dan bagaimana...')
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    
                    $this->record->update([
                        'status' => 'membutuhkan_revisi',
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);

                    $this->sendStatusUpdateNotification($oldStatus, 'membutuhkan_revisi', $data['catatan_petugas']);
                    $this->fillForm();

                    Notification::make()
                        ->title('Permintaan revisi berhasil dikirim')
                        ->success()
                        ->send();
                })
                ->visible(fn () => !in_array($this->record->status, ['disetujui', 'selesai', 'ditolak'])),

            // Action untuk lihat revisi
            Actions\Action::make('view_revisions')
                ->label('Kelola Revisi')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->url(fn () => route('filament.petugas.resources.permohonan-revisions.index', [
                    'tableFilters[permohonan_id][value]' => $this->record->id
                ]))
                ->visible(fn () => $this->record->revisions()->exists()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Informasi Permohonan
                Section::make('Informasi Permohonan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_permohonan')
                            ->label('Kode Permohonan')
                            ->icon('heroicon-s-document-text')
                            ->copyable(),
                        TextEntry::make('user.name')
                            ->label('Nama Warga')
                            ->icon('heroicon-s-user'),
                        TextEntry::make('layanan.name')
                            ->label('Jenis Layanan')
                            ->icon('heroicon-s-clipboard-document-list'),
                        TextEntry::make('data_pemohon.jenis_permohonan')
                            ->label('Jenis Permohonan')
                            ->icon('heroicon-s-tag'),
                        TextEntry::make('status')
                            ->label('Status Permohonan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru' => 'gray',
                                'sedang_ditinjau' => 'info',
                                'verifikasi_berkas' => 'warning',
                                'diproses' => 'info',
                                'membutuhkan_revisi' => 'danger',
                                'butuh_perbaikan' => 'danger',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'primary',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'baru' => 'Baru Diajukan',
                                'sedang_ditinjau' => 'Sedang Ditinjau',
                                'verifikasi_berkas' => 'Verifikasi Berkas',
                                'diproses' => 'Sedang Diproses',
                                'membutuhkan_revisi' => 'Membutuhkan Revisi',
                                'butuh_perbaikan' => 'Butuh Perbaikan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'selesai' => 'Selesai',
                                default => $state,
                            }),
                        TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime()
                            ->icon('heroicon-s-calendar'),
                    ]),

                // Catatan Petugas
                Section::make('Catatan Petugas')
                    ->schema([
                        TextEntry::make('catatan_petugas')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Belum ada catatan dari petugas'),
                    ])
                    ->visible(fn ($record) => !empty($record->catatan_petugas)),

                // Detail Data Diri Pemohon
                Section::make('Detail Data Diri Pemohon')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.nik')->label('NIK'),
                        TextEntry::make('user.nomor_kk')->label('Nomor KK'),
                        TextEntry::make('user.nomor_whatsapp')->label('Nomor WhatsApp'),
                        TextEntry::make('user.jenis_kelamin')->label('Jenis Kelamin'),
                        TextEntry::make('user.agama')->label('Agama'),
                        TextEntry::make('user.tempat_lahir')->label('Tempat Lahir'),
                        TextEntry::make('user.tanggal_lahir')->label('Tanggal Lahir')->date(),
                        TextEntry::make('user.gol_darah')->label('Golongan Darah'),
                        TextEntry::make('user.alamat')->label('Alamat Lengkap')->columnSpanFull(),
                        TextEntry::make('user.rt_rw')->label('RT/RW'),
                        TextEntry::make('user.desa_kelurahan')->label('Desa/Kelurahan'),
                        TextEntry::make('user.kecamatan')->label('Kecamatan'),
                        TextEntry::make('user.kabupaten')->label('Kabupaten'),
                        TextEntry::make('user.status_keluarga')->label('Status dalam Keluarga'),
                        TextEntry::make('user.status_perkawinan')->label('Status Perkawinan'),
                        TextEntry::make('user.pekerjaan')->label('Pekerjaan'),
                        TextEntry::make('user.pendidikan')->label('Pendidikan Terakhir'),
                    ]),

                // Berkas Permohonan Asli
                Section::make('Berkas Permohonan Asli')
                    ->schema(function ($record) {
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

                // Riwayat Revisi (Manual Implementation)
                Section::make('Riwayat Revisi')
                    ->schema([
                        RepeatableEntry::make('revisions')
                            ->label('')
                            ->schema([
                                TextEntry::make('revision_number')
                                    ->label('Revisi Ke')
                                    ->badge()
                                    ->color('primary'),
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
                                TextEntry::make('created_at')
                                    ->label('Tanggal Kirim')
                                    ->dateTime()
                                    ->since(),
                                TextEntry::make('catatan_revisi')
                                    ->label('Catatan Warga')
                                    ->limit(100)
                                    ->tooltip(fn ($state) => $state),
                                TextEntry::make('catatan_petugas')
                                    ->label('Catatan Petugas')
                                    ->limit(100)
                                    ->tooltip(fn ($state) => $state)
                                    ->visible(fn ($state) => !empty($state)),
                                TextEntry::make('reviewedBy.name')
                                    ->label('Direview Oleh')
                                    ->default('Belum direview')
                                    ->badge()
                                    ->color('gray'),

                                // Actions untuk setiap revisi
                                InfolistActions::make([
                                    InfolistAction::make('review_revision')
                                        ->label('Review')
                                        ->icon('heroicon-s-pencil-square')
                                        ->color('warning')
                                        ->form([
                                            Select::make('status')
                                                ->label('Status Review')
                                                ->options([
                                                    'reviewed' => 'Sudah Direview',
                                                    'accepted' => 'Diterima',
                                                    'rejected' => 'Ditolak',
                                                ])
                                                ->required()
                                                ->native(false),
                                            Textarea::make('catatan_petugas')
                                                ->label('Catatan Review')
                                                ->required()
                                                ->rows(3),
                                        ])
                                        ->action(function (PermohonanRevision $record, array $data) {
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

                                            Notification::make()
                                                ->title('Revisi berhasil direview')
                                                ->success()
                                                ->send();

                                            return redirect(request()->header('Referer'));
                                        })
                                        ->visible(fn (PermohonanRevision $record) => $record->status === 'pending'),

                                    InfolistAction::make('view_files')
                                        ->label('Lihat Berkas')
                                        ->icon('heroicon-s-eye')
                                        ->color('info')
                                        ->url(fn (PermohonanRevision $record) => route('filament.petugas.resources.permohonan-revisions.view', $record))
                                        ->openUrlInNewTab(),
                                ]),
                            ])
                            ->columns(3)
                            ->contained(false),
                    ])
                    ->visible(fn ($record) => $record->revisions()->exists())
                    ->collapsible(),

                // Update & Timestamps
                Section::make('Informasi Sistem')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime()
                            ->since(),
                    ])
                    ->collapsed(),
            ]);
    }

    /**
     * Send status update notification to the user who submitted the request
     */
    protected function sendStatusUpdateNotification(string $oldStatus = null, string $newStatus = null, string $catatan = null): void
    {
        $permohonan = $this->record->fresh();
        $user = $permohonan->user;
        
        if (!$user) {
            return;
        }

        $currentStatus = $newStatus ?? $permohonan->status;
        $statusLabels = [
            'baru' => 'Baru Diajukan',
            'sedang_ditinjau' => 'Sedang Ditinjau',
            'verifikasi_berkas' => 'Verifikasi Berkas',
            'diproses' => 'Sedang Diproses',
            'membutuhkan_revisi' => 'Membutuhkan Revisi',
            'butuh_perbaikan' => 'Butuh Perbaikan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
        ];

        $statusLabel = $statusLabels[$currentStatus] ?? $currentStatus;
        $notificationIcon = match($currentStatus) {
            'disetujui' => 'heroicon-o-check-circle',
            'ditolak' => 'heroicon-o-x-circle',
            'membutuhkan_revisi', 'butuh_perbaikan' => 'heroicon-o-exclamation-triangle',
            'selesai' => 'heroicon-o-flag',
            default => 'heroicon-o-information-circle',
        };

        $notificationColor = match($currentStatus) {
            'disetujui', 'selesai' => 'success',
            'ditolak' => 'danger',
            'membutuhkan_revisi', 'butuh_perbaikan' => 'warning',
            default => 'info',
        };

        $body = "Status permohonan Anda dengan kode {$permohonan->kode_permohonan} telah diperbarui menjadi: {$statusLabel}";
        
        if ($catatan ?? $permohonan->catatan_petugas) {
            $body .= "\n\nCatatan: " . ($catatan ?? $permohonan->catatan_petugas);
        }

        Notification::make()
            ->title('Status Permohonan Diperbarui')
            ->icon($notificationIcon)
            ->body($body)
            ->color($notificationColor)
            ->sendToDatabase($user);
    }
}