<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use App\Models\Permohonan;
use App\Models\PermohonanRevision;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // HEADER SECTION - Informasi Utama
                InfolistSection::make('Informasi Permohonan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('kode_permohonan')
                                    ->label('Kode Permohonan')
                                    ->copyable()
                                    ->copyMessage('Kode permohonan disalin!')
                                    ->icon('heroicon-s-hashtag')
                                    ->color('primary')
                                    ->weight('bold'),

                                TextEntry::make('data_pemohon.jenis_permohonan')
                                    ->label('Jenis Permohonan')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'baru' => 'gray',
                                        'sedang_ditinjau' => 'warning',
                                        'verifikasi_berkas' => 'info',
                                        'diproses' => 'primary',
                                        'membutuhkan_revisi' => 'danger',
                                        'butuh_perbaikan' => 'warning',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        'selesai' => 'success',
                                        default => 'gray',
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
                            ]),

                        InfolistGrid::make(4)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Tanggal Diajukan')
                                    ->dateTime('d M Y H:i')
                                    ->icon('heroicon-s-calendar'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Update')
                                    ->since()
                                    ->icon('heroicon-s-clock'),

                                TextEntry::make('assigned_to')
                                    ->label('Ditugaskan ke')
                                    ->getStateUsing(fn (Permohonan $record) => 
                                        $record->assignedTo ? $record->assignedTo->name : 'Belum Ditugaskan'
                                    )
                                    ->badge()
                                    ->color(fn (Permohonan $record) => $record->assigned_to ? 'success' : 'warning'),

                                TextEntry::make('priority_level')
                                    ->label('Prioritas')
                                    ->getStateUsing(function (Permohonan $record) {
                                        $hours = now()->diffInHours($record->created_at);
                                        if ($hours > 72) return 'Tinggi';
                                        if ($hours > 24) return 'Sedang';
                                        return 'Normal';
                                    })
                                    ->badge()
                                    ->color(function (Permohonan $record) {
                                        $hours = now()->diffInHours($record->created_at);
                                        if ($hours > 72) return 'danger';
                                        if ($hours > 24) return 'warning';
                                        return 'success';
                                    }),
                            ]),
                    ]),

                // DATA PEMOHON SECTION
                InfolistSection::make('Data Pemohon')
                    ->icon('heroicon-o-user')
                    ->schema([
                        InfolistGrid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama Lengkap')
                                    ->icon('heroicon-s-user'),

                                TextEntry::make('user.nik')
                                    ->label('NIK')
                                    ->icon('heroicon-s-identification'),

                                TextEntry::make('user.nomor_kk')
                                    ->label('No. KK')
                                    ->icon('heroicon-s-home'),

                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->icon('heroicon-s-envelope')
                                    ->copyable(),

                                TextEntry::make('user.nomor_telepon')
                                    ->label('No. Telepon')
                                    ->icon('heroicon-s-phone')
                                    ->copyable(),

                                TextEntry::make('user.alamat')
                                    ->label('Alamat')
                                    ->icon('heroicon-s-map-pin')
                                    ->columnSpan(1),
                            ]),
                    ]),

                // CATATAN PETUGAS SECTION
                InfolistSection::make('Catatan & Komunikasi')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextEntry::make('catatan_petugas')
                            ->label('Catatan Petugas')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Belum ada catatan.')
                            ->color(fn (Permohonan $record) => match($record->status) {
                                'membutuhkan_revisi', 'butuh_perbaikan', 'ditolak' => 'danger',
                                'disetujui', 'selesai' => 'success',
                                default => 'primary',
                            }),
                    ])
                    ->visible(fn (Permohonan $record) => !empty($record->catatan_petugas)),

                // LAYOUT GRID UNTUK KONTEN UTAMA
                InfolistGrid::make(12)
                    ->schema([
                        // KOLOM KIRI - BERKAS & REVISI (span 8)
                        InfolistGroup::make()
                            ->schema([
                                // BERKAS PERMOHONAN AWAL
                                InfolistSection::make('Berkas Permohonan Awal')
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->collapsible()
                                    ->schema(function (Permohonan $record) {
                                        $berkasFields = [];
                                        if (is_array($record->berkas_pemohon)) {
                                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                                if (empty($berkas['path_dokumen'])) continue;
                                                $berkasFields[] = TextEntry::make("berkas_awal_{$index}")
                                                    ->label($berkas['nama_dokumen'] ?? "Dokumen " . ($index + 1))
                                                    ->getStateUsing(function () use ($berkas, $record) {
                                                        $downloadUrl = route('secure.download', [
                                                            'permohonan_id' => $record->id,
                                                            'path' => $berkas['path_dokumen']
                                                        ]);
                                                        return view('filament.infolists.components.download-link', [
                                                            'url' => $downloadUrl,
                                                            'filename' => basename($berkas['path_dokumen']),
                                                            'filePath' => $berkas['path_dokumen'], // Pass the actual file path
                                                            'label' => 'Unduh Dokumen',
                                                            'icon' => 'heroicon-o-arrow-down-tray'
                                                        ])->render();
                                                    })
                                                    ->html()
                                                    ->columnSpanFull();
                                            }
                                        }
                                        return $berkasFields ?: [
                                            TextEntry::make('no_files')
                                                ->label('')
                                                ->getStateUsing(fn () => 'Tidak ada berkas yang diupload.')
                                                ->color('warning')
                                        ];
                                    }),

                                // RIWAYAT REVISI DARI WARGA
                                InfolistSection::make('Riwayat Revisi dari Warga')
                                    ->icon('heroicon-o-arrow-path')
                                    ->collapsible()
                                    ->schema([
                                        ViewEntry::make('revisions')
                                            ->label('')
                                            ->view('filament.infolists.components.revision-history')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Permohonan $record) => $record->revisions()->count() > 0),

                                // DETAIL REVISI AKTIF
                                InfolistSection::make('Detail Revisi Terbaru')
                                    ->icon('heroicon-o-document-plus')
                                    ->schema(function (Permohonan $record) {
                                        $latestRevision = $record->revisions()->latest()->first();
                                        if (!$latestRevision) {
                                            return [
                                                TextEntry::make('no_revision')
                                                    ->label('')
                                                    ->getStateUsing(fn () => 'Belum ada revisi yang diajukan.')
                                                    ->color('gray')
                                            ];
                                        }

                                        $schema = [
                                            InfolistGrid::make(3)
                                                ->schema([
                                                    TextEntry::make('revision_number')
                                                        ->label('Revisi ke-')
                                                        ->getStateUsing(fn () => $latestRevision->revision_number)
                                                        ->badge()
                                                        ->color('info'),

                                                    TextEntry::make('revision_status')
                                                        ->label('Status Revisi')
                                                        ->getStateUsing(fn () => match($latestRevision->status) {
                                                            'pending' => 'Menunggu Review',
                                                            'approved' => 'Diterima',
                                                            'rejected' => 'Ditolak',
                                                            default => $latestRevision->status
                                                        })
                                                        ->badge()
                                                        ->color(fn () => match($latestRevision->status) {
                                                            'pending' => 'warning',
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            default => 'gray'
                                                        }),

                                                    TextEntry::make('revision_date')
                                                        ->label('Tanggal Revisi')
                                                        ->getStateUsing(fn () => $latestRevision->created_at->format('d M Y H:i'))
                                                        ->icon('heroicon-s-calendar'),
                                                ]),

                                            TextEntry::make('revision_notes')
                                                ->label('Catatan Revisi dari Warga')
                                                ->getStateUsing(fn () => $latestRevision->catatan_revisi ?: 'Tidak ada catatan.')
                                                ->markdown()
                                                ->columnSpanFull(),
                                        ];

                                        // Tampilkan berkas revisi
                                        if (is_array($latestRevision->berkas_revisi)) {
                                            $schema[] = TextEntry::make('revision_files_label')
                                                ->label('Berkas Revisi:')
                                                ->getStateUsing(fn () => '')
                                                ->columnSpanFull();

                                            foreach ($latestRevision->berkas_revisi as $index => $berkas) {
                                                if (empty($berkas['path_dokumen'])) continue;
                                                $schema[] = TextEntry::make("revision_file_{$index}")
                                                    ->label($berkas['nama_dokumen'] ?? "Dokumen Revisi " . ($index + 1))
                                                    ->getStateUsing(function () use ($berkas, $latestRevision) {
                                                        $downloadUrl = route('secure.download.revision', [
                                                            'revision_id' => $latestRevision->id,
                                                            'path' => $berkas['path_dokumen']
                                                        ]);
                                                        return view('filament.infolists.components.download-link', [
                                                            'url' => $downloadUrl,
                                                            'filename' => basename($berkas['path_dokumen']),
                                                            'filePath' => $berkas['path_dokumen'], // Pass the actual file path
                                                            'label' => 'Unduh Berkas Revisi',
                                                            'icon' => 'heroicon-o-arrow-down-tray',
                                                            'color' => 'warning'
                                                        ])->render();
                                                    })
                                                    ->html()
                                                    ->columnSpanFull();
                                            }
                                        }

                                        // Catatan petugas untuk revisi
                                        if (!empty($latestRevision->catatan_petugas)) {
                                            $schema[] = TextEntry::make('revision_petugas_notes')
                                                ->label('Catatan Petugas untuk Revisi')
                                                ->getStateUsing(fn () => $latestRevision->catatan_petugas)
                                                ->markdown()
                                                ->color(fn () => match($latestRevision->status) {
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    default => 'primary'
                                                })
                                                ->columnSpanFull();
                                        }

                                        return $schema;
                                    })
                                    ->visible(fn (Permohonan $record) => $record->revisions()->count() > 0),
                            ])
                            ->columnSpan(8),

                        // KOLOM KANAN - TIMELINE & STATISTIK (span 4)
                        InfolistGroup::make()
                            ->schema([
                                // TIMELINE LOG
                                InfolistSection::make('Timeline Permohonan')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        ViewEntry::make('logs')
                                            ->label('')
                                            ->view('filament.infolists.components.timeline-log'),
                                    ]),

                                // STATISTIK PERMOHONAN
                                InfolistSection::make('Statistik')
                                    ->icon('heroicon-o-chart-bar')
                                    ->schema([
                                        TextEntry::make('processing_time')
                                            ->label('Waktu Proses')
                                            ->getStateUsing(function (Permohonan $record) {
                                                $hours = now()->diffInHours($record->created_at);
                                                $days = floor($hours / 24);
                                                $remainingHours = $hours % 24;
                                                
                                                if ($days > 0) {
                                                    return "{$days} hari {$remainingHours} jam";
                                                }
                                                return "{$hours} jam";
                                            })
                                            ->icon('heroicon-s-clock'),

                                        TextEntry::make('revision_count')
                                            ->label('Jumlah Revisi')
                                            ->getStateUsing(fn (Permohonan $record) => $record->revisions()->count())
                                            ->badge()
                                            ->color(fn (Permohonan $record) => $record->revisions()->count() > 2 ? 'warning' : 'success'),

                                        TextEntry::make('last_activity')
                                            ->label('Aktivitas Terakhir')
                                            ->getStateUsing(function (Permohonan $record) {
                                                $lastRevision = $record->revisions()->latest()->first();
                                                if ($lastRevision) {
                                                    return $lastRevision->created_at->diffForHumans();
                                                }
                                                return $record->updated_at->diffForHumans();
                                            })
                                            ->icon('heroicon-s-arrow-path'),
                                    ]),

                                // QUICK ACTIONS
                                InfolistSection::make('Aksi Cepat')
                                    ->icon('heroicon-o-bolt')
                                    ->schema([
                                        ViewEntry::make('quick_actions')
                                            ->label('')
                                            ->view('filament.infolists.components.quick-actions')
                                            ->viewData([
                                                'record' => fn (Permohonan $record) => $record
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(4),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // ASSIGN PERMOHONAN
            Action::make('assign_to_me')
                ->label('Ambil Tugas')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->action(function (): void {
                    try {
                        $this->record->update([
                            'assigned_to' => Auth::id(),
                            'status' => 'sedang_ditinjau',
                            'catatan_petugas' => 'Permohonan telah ditugaskan dan sedang dalam peninjauan.',
                        ]);

                        Notification::make()
                            ->title('Tugas Berhasil Diambil!')
                            ->body("Anda sekarang bertanggung jawab untuk permohonan {$this->record->kode_permohonan}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Terjadi Kesalahan')
                            ->body('Tidak dapat mengambil tugas permohonan ini.')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => $this->record->canBeAssignedTo())
                ->requiresConfirmation()
                ->modalHeading('Ambil Tugas Permohonan')
                ->modalDescription(fn () => 
                    "Apakah Anda yakin ingin mengambil tugas untuk menangani permohonan {$this->record->kode_permohonan}? " .
                    "Setelah mengambil tugas, Anda akan bertanggung jawab untuk memproses permohonan ini."
                )
                ->modalSubmitActionLabel('Ya, Ambil Tugas'),

            // UPDATE STATUS
            Action::make('update_status')
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
                            $set('catatan_petugas', $defaultMessages[$state] ?? '');
                        }),

                    Forms\Components\Textarea::make('catatan_petugas')
                        ->label('Catatan untuk Warga')
                        ->placeholder('Tambahkan catatan atau keterangan untuk warga...')
                        ->rows(4)
                        ->required(fn (Forms\Get $get) => 
                            in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak'])
                        )
                        ->helperText(fn (Forms\Get $get) => 
                            in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak']) 
                                ? 'Catatan wajib diisi untuk status ini.' 
                                : 'Catatan opsional yang akan dilihat warga.'
                        ),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => $data['status'],
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);

                    // Kirim notifikasi ke warga
                    $statusLabels = [
                        'sedang_ditinjau' => 'Sedang Ditinjau',
                        'verifikasi_berkas' => 'Verifikasi Berkas',
                        'diproses' => 'Sedang Diproses',
                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
                        'butuh_perbaikan' => 'Butuh Perbaikan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                    ];

                    Notification::make()
                        ->title('Status Permohonan Diperbarui')
                        ->body("Status permohonan {$this->record->kode_permohonan} diubah menjadi: {$statusLabels[$data['status']]}")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Status Berhasil Diperbarui!')
                        ->success()
                        ->send();
                })
                ->modalHeading('Update Status Permohonan')
                ->modalDescription('Perbarui status permohonan dan berikan catatan kepada warga.')
                ->modalSubmitActionLabel('Update Status'),

            // REVIEW REVISI TERBARU
            Action::make('review_latest_revision')
                ->label('Review Revisi')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('filament.petugas.resources.permohonan-revisions.index', [
                    'tableFilters[permohonan_id][value]' => $this->record->id
                ]))
                ->visible(fn () => $this->record->revisions()->where('status', 'pending')->count() > 0),

            // ASSIGN KE PETUGAS LAIN
            Action::make('assign_to_other')
                ->label('Tugaskan ke Petugas Lain')
                ->icon('heroicon-o-user-group')
                ->color('gray')
                ->form([
                    Select::make('assigned_to')
                        ->label('Pilih Petugas')
                        ->options(function () {
                            return User::role(['petugas', 'admin'])
                                ->where('id', '!=', $this->record->assigned_to)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\Textarea::make('assignment_note')
                        ->label('Catatan Assignment')
                        ->placeholder('Tambahkan catatan untuk petugas yang ditugaskan...')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $newAssignee = User::find($data['assigned_to']);
                    
                    $this->record->update([
                        'assigned_to' => $data['assigned_to'],
                        'catatan_petugas' => $this->record->catatan_petugas . "\n\n[Assignment] Ditugaskan ulang ke {$newAssignee->name}: " . ($data['assignment_note'] ?? ''),
                    ]);

                    // Notifikasi ke petugas baru
                    Notification::make()
                        ->title('Permohonan Baru Ditugaskan')
                        ->body("Anda ditugaskan untuk menangani permohonan {$this->record->kode_permohonan}")
                        ->info()
                        ->sendToDatabase($newAssignee);

                    Notification::make()
                        ->title('Berhasil Ditugaskan!')
                        ->body("Permohonan berhasil ditugaskan ke {$newAssignee->name}")
                        ->success()
                        ->send();
                })
                ->visible(fn () => Auth::user()->hasRole(['admin']) || $this->record->assigned_to === Auth::id())
                ->modalHeading('Tugaskan ke Petugas Lain')
                ->modalSubmitActionLabel('Tugaskan'),
        ];
    }
}