<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\Auth;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ===================
            // ASSIGNMENT ACTIONS
            // ===================
            
            Action::make('ambil_tugas')
                ->label('Ambil Tugas')
                ->icon('heroicon-o-hand-raised')
                ->color('primary')
                ->action(function () {
                    $success = $this->record->assignTo(auth()->id());
                    
                    if ($success) {
                        // Update status jika masih baru
                        if ($this->record->status === 'baru') {
                            $this->record->update([
                                'status' => 'sedang_ditinjau',
                                'catatan_petugas' => 'Permohonan telah diambil oleh ' . auth()->user()->name . ' dan sedang dalam tahap peninjauan.'
                            ]);
                        }

                        $this->fillForm();

                        Notification::make()
                            ->title('Tugas berhasil diambil')
                            ->body('Permohonan sekarang menjadi tanggung jawab Anda.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal mengambil tugas')
                            ->body('Terjadi kesalahan saat mengambil tugas.')
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

            // ======================
            // STATUS UPDATE ACTIONS
            // ======================
            
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
                    
                    // Auto-assign jika belum di-assign dan bukan status yang mengindikasikan selesai
                    if (!$this->record->isAssigned() && !in_array($data['status'], ['ditolak', 'selesai'])) {
                        $this->record->assignTo(auth()->id());
                    }
                    
                    $this->record->update([
                        'status' => $data['status'],
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);

                    // Send notification to user
                    $this->sendStatusUpdateNotification($oldStatus, $data['status'], $data['catatan_petugas']);

                    // Refresh the page data
                    $this->fillForm();

                    Notification::make()
                        ->title('Status berhasil diperbarui')
                        ->body('Notifikasi telah dikirim kepada pemohon.')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => true) // Selalu tampil untuk semua petugas
                ->modalHeading('Update Status Permohonan')
                ->modalDescription('Update status akan mengirim notifikasi kepada pemohon')
                ->modalSubmitActionLabel('Update & Kirim Notifikasi'),

            // =================
            // QUICK ACTIONS
            // =================
            
            Action::make('approve')
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
                    
                    // Auto-assign jika belum di-assign
                    if (!$this->record->isAssigned()) {
                        $this->record->assignTo(auth()->id());
                    }
                    
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

            Action::make('reject')
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
                    
                    // Auto-assign jika belum di-assign
                    if (!$this->record->isAssigned()) {
                        $this->record->assignTo(auth()->id());
                    }
                    
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

            // ===================
            // ASSIGNMENT MANAGEMENT
            // ===================
            
            Action::make('alihkan_tugas')
                ->label('Alihkan Tugas')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('warning')
                ->form([
                    Select::make('assigned_to')
                        ->label('Alihkan Ke Petugas')
                        ->options(function () {
                            return User::role(['petugas', 'admin'])
                                ->where('id', '!=', auth()->id())
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable()
                        ->preload()
                        ->helperText('Pilih petugas yang akan menangani permohonan ini'),
                    
                    Textarea::make('catatan_pengalihan')
                        ->label('Catatan Pengalihan')
                        ->placeholder('Berikan catatan khusus untuk petugas yang akan menangani...')
                        ->rows(3)
                        ->helperText('Catatan ini akan membantu petugas baru memahami kondisi permohonan'),
                ])
                ->action(function (array $data) {
                    $newAssignee = User::find($data['assigned_to']);
                    $oldAssignee = $this->record->assignedTo;
                    
                    $success = $this->record->reassignTo($data['assigned_to'], $data['catatan_pengalihan']);
                    
                    if ($success) {
                        // Update catatan jika ada
                        if (!empty($data['catatan_pengalihan'])) {
                            $currentNote = $this->record->catatan_petugas ?? '';
                            $newNote = $currentNote . "\n\n[PENGALIHAN] Dialihkan dari " . $oldAssignee?->name . " ke " . $newAssignee->name . " oleh " . auth()->user()->name . ":\n" . $data['catatan_pengalihan'];
                            $this->record->update(['catatan_petugas' => $newNote]);
                        }

                        // Kirim notifikasi ke petugas baru
                        Notification::make()
                            ->title('Tugas Baru: ' . $this->record->kode_permohonan)
                            ->body("Anda telah ditugaskan untuk menangani permohonan ini. Catatan: " . ($data['catatan_pengalihan'] ?? 'Tidak ada catatan khusus.'))
                            ->success()
                            ->sendToDatabase($newAssignee);

                        $this->fillForm();

                        Notification::make()
                            ->title('Tugas berhasil dialihkan')
                            ->body("Permohonan telah dialihkan ke {$newAssignee->name}")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal mengalihkan tugas')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn (): bool => 
                    $this->record->isAssigned() && 
                    (auth()->user()->hasRole('admin') || $this->record->isAssignedTo(auth()->id()))
                )
                ->modalHeading('Alihkan Tugas Permohonan')
                ->modalSubmitActionLabel('Alihkan Tugas'),

            Action::make('batal_assignment')
                ->label('Batalkan Assignment')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Textarea::make('reason')
                        ->label('Alasan Pembatalan')
                        ->required()
                        ->placeholder('Jelaskan mengapa assignment dibatalkan...')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $success = $this->record->unassign($data['reason']);
                    
                    if ($success) {
                        $this->fillForm();

                        Notification::make()
                            ->title('Assignment berhasil dibatalkan')
                            ->success()
                            ->send();
                    }
                })
                ->visible(fn (): bool => 
                    $this->record->isAssigned() && auth()->user()->hasRole('admin')
                )
                ->requiresConfirmation()
                ->modalHeading('Batalkan Assignment'),

            // =================
            // NAVIGATION ACTIONS
            // =================
            
            Actions\EditAction::make()
                ->label('Edit Detail')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn (): bool => 
                    $this->record->isAssignedTo(auth()->id()) || 
                    auth()->user()->hasRole(['admin', 'kadis'])
                ),
        ];
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

        $body = "Permohonan dengan kode {$permohonan->kode_permohonan} {$statusLabel}";
        
        if ($catatan ?? $permohonan->catatan_petugas) {
            $body .= "\n\n" . ($catatan ?? $permohonan->catatan_petugas);
        }

        // Send notification to user
        Notification::make()
            ->title('Status Permohonan Diperbarui')
            ->icon($notificationIcon)
            ->body($body)
            ->color($notificationColor)
            ->actions([
             NotificationAction::make('Lihat Detail')
                    ->button()
                    ->url(route('filament.warga.resources.permohonans.view', ['record' => $this->record->kode_permohonan]), shouldOpenInNewTab: false)
            ])
            ->sendToDatabase($user);
    }

    /**
     * Get the form model
     */
    protected function getFormModel(): string
    {
        return $this->record::class;
    }

    /**
     * Customize the view data
     */
    protected function getViewData(): array
    {
        $data = parent::getViewData();
        
        // Add assignment information
        $data['assignmentInfo'] = [
            'isAssigned' => $this->record->isAssigned(),
            'assignedTo' => $this->record->assignedTo,
            'assignedAt' => $this->record->assigned_at,
            'assignedBy' => $this->record->assignedBy,
            'assignmentDuration' => $this->record->assignment_duration ?? null,
            'isOverdue' => method_exists($this->record, 'isAssignmentOverdue') ? $this->record->isAssignmentOverdue() : false,
            'canBeAssigned' => $this->record->canBeAssignedTo(),
            'currentUserWorkload' => method_exists(\App\Models\Permohonan::class, 'getPetugasWorkload') ? \App\Models\Permohonan::getPetugasWorkload(auth()->id()) : 0,
        ];

        // Add workload distribution for admins
        if (auth()->user()->hasRole('admin')) {
            $data['workloadDistribution'] = method_exists(\App\Models\Permohonan::class, 'getWorkloadDistribution') ? \App\Models\Permohonan::getWorkloadDistribution() : [];
        }

        return $data;
    }
}