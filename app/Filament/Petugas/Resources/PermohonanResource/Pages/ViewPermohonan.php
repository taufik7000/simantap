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
use Illuminate\Support\Str;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // LAYOUT GRID 2 KOLOM
                InfolistGrid::make(12)
                    ->schema([
                        // KOLOM KIRI - INFORMASI & DATA PEMOHON (span 8)
                        InfolistGroup::make()
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

                                                TextEntry::make('user.nomor_whatsapp')
                                                    ->label('No. WA')
                                                    ->icon('heroicon-s-phone')
                                                    ->copyable(),

                                                TextEntry::make('user.alamat')
                                                    ->label('Alamat')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->columnSpan(1),

                                                TextEntry::make('user.jenis_kelamin')
                                                    ->label('Jenis Kelamin')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->columnSpan(1),

                                                TextEntry::make('user.desa_kelurahan')
                                                    ->label('Desa/Kelurahan')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->columnSpan(1),
                                                
                                                TextEntry::make('user.kecamatan')
                                                    ->label('Kecamatan')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->columnSpan(1),
                                                
                                                TextEntry::make('user.kabupaten')
                                                    ->label('Kabupaten')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->columnSpan(1),
                                            ]),
                                    ]),

                                // TAMBAHAN: DATA ISIAN FORMULIR SECTION
                                InfolistSection::make('Data Isian Formulir')
                                    ->icon('heroicon-o-pencil-square')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema(function (Permohonan $record) {
                                        $fields = [];
                                        $jenisPermohonan = $record->data_pemohon['jenis_permohonan'] ?? null;
                                        
                                        if ($jenisPermohonan && $record->layanan?->description) {
                                            $formDefinition = collect($record->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);
                                            
                                            if (!empty($formDefinition['form_fields'])) {
                                                foreach ($formDefinition['form_fields'] as $fieldDef) {
                                                    $fieldName = $fieldDef['field_name'];
                                                    if (isset($record->data_pemohon[$fieldName])) {
                                                        $fields[] = TextEntry::make("data_pemohon.{$fieldName}")
                                                            ->label($fieldDef['field_label'])
                                                            ->copyable()
                                                            ->icon('heroicon-s-pencil');
                                                    }
                                                }
                                            }
                                        }
                                        
                                        return empty($fields) ? [
                                            TextEntry::make('no_data_isian')
                                                ->label('')
                                                ->getStateUsing(fn () => 'Tidak ada data isian tambahan untuk permohonan ini.')
                                                ->color('gray')
                                                ->icon('heroicon-s-information-circle')
                                        ] : $fields;
                                    })
                                    ->columns(2),

                                // RIWAYAT REVISI DARI WARGA
                                InfolistSection::make('Riwayat Revisi dari Warga')
                                    ->icon('heroicon-o-arrow-path')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        ViewEntry::make('revisions')
                                            ->label('')
                                            ->view('filament.infolists.components.revision-history')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Permohonan $record) => $record->revisions()->count() > 0),
                            ])
                            ->columnSpan(8),

                        // KOLOM KANAN - STATISTIK, BERKAS, AKSI, TIMELINE (span 4)
                        InfolistGroup::make()
                            ->schema([
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

                                // BERKAS PERMOHONAN
                                InfolistSection::make('Berkas Permohonan Awal')
                                    ->schema(function (Permohonan $record) {
                                        $berkasFields = [];
                                        $jenisPermohonan = $record->data_pemohon['jenis_permohonan'] ?? null;
                                        
                                        if (!$jenisPermohonan || !$record->layanan?->description) {
                                            return [TextEntry::make('no_berkas')->state('Definisi layanan tidak ditemukan.')];
                                        }
                                        
                                        $jenisData = collect($record->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);

                                        if (empty($jenisData['file_requirements'])) {
                                            return [TextEntry::make('no_berkas')->state('Tidak ada syarat berkas untuk permohonan ini.')];
                                        }

                                        foreach ($jenisData['file_requirements'] as $fileReq) {
                                            $fileKey = $fileReq['file_key'];
                                            $filePath = $record->berkas_pemohon[$fileKey] ?? null;
                                            
                                            $entry = TextEntry::make($fileKey)->label($fileReq['file_name']);

                                            if ($filePath) {
                                                $entry->state('Unduh Berkas')
                                                      ->color('primary')
                                                      ->url(route('secure.download', ['permohonan_id' => $record->id, 'path' => $filePath]), true)
                                                      ->icon('heroicon-m-arrow-down-tray');
                                            } else {
                                                $entry->state('Tidak diunggah')
                                                      ->color('danger')
                                                      ->icon('heroicon-o-x-circle');
                                            }
                                            $berkasFields[] = $entry;
                                        }
                                        return $berkasFields;
                                    }),

                                // DETAIL REVISI TERBARU
                                InfolistSection::make('Revisi Terbaru')
                                    ->icon('heroicon-o-document-plus')
                                    ->collapsible()
                                    ->collapsed()
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
                                            InfolistGrid::make(2)
                                                ->schema([
                                                    TextEntry::make('revision_number')
                                                        ->label('Revisi ke-')
                                                        ->getStateUsing(fn () => $latestRevision->revision_number)
                                                        ->badge()
                                                        ->color('info'),

                                                    TextEntry::make('revision_status')
                                                        ->label('Status')
                                                        ->getStateUsing(fn () => match($latestRevision->status) {
                                                            'pending' => 'Menunggu Review',
                                                            'accepted' => 'Diterima',
                                                            'rejected' => 'Ditolak',
                                                            default => $latestRevision->status
                                                        })
                                                        ->badge()
                                                        ->color(fn () => match($latestRevision->status) {
                                                            'pending' => 'warning',
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger',
                                                            default => 'gray'
                                                        }),
                                                ]),

                                            TextEntry::make('revision_notes')
                                                ->label('Catatan Warga')
                                                ->getStateUsing(fn () => $latestRevision->catatan_revisi ?: 'Tidak ada catatan.')
                                                ->markdown()
                                                ->columnSpanFull(),
                                        ];

                                        // Tampilkan berkas revisi (ringkas)
                                        if (is_array($latestRevision->berkas_revisi) && count($latestRevision->berkas_revisi) > 0) {
                                            $schema[] = TextEntry::make('revision_files_count')
                                                ->label('Berkas Revisi')
                                                ->getStateUsing(fn () => count($latestRevision->berkas_revisi) . ' file diupload')
                                                ->badge()
                                                ->color('warning')
                                                ->columnSpanFull();
                                        }

                                        return $schema;
                                    })
                                    ->visible(fn (Permohonan $record) => $record->revisions()->count() > 0),

                                // QUICK ACTIONS
                                InfolistSection::make('Aksi Cepat')
                                    ->icon('heroicon-o-bolt')
                                    ->collapsible()
                                    ->schema([
                                        ViewEntry::make('quick_actions')
                                            ->label('')
                                            ->view('filament.infolists.components.quick-actions')
                                            ->viewData([
                                                'record' => fn (Permohonan $record) => $record
                                            ]),
                                    ]),

                                // TIMELINE LOG - MODERN VERSION
                                InfolistSection::make('Timeline Permohonan')
                                    ->icon('heroicon-o-clock')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        ViewEntry::make('modern_timeline')
                                            ->label('')
                                            ->view('filament.infolists.components.modern-timeline'),
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