<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action untuk quick status update
            Action::make('quickStatusUpdate')
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
                            // Auto-fill catatan berdasarkan status
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
                ->modalHeading('Update Status Permohonan')
                ->modalDescription('Update status akan mengirim notifikasi kepada pemohon')
                ->modalSubmitActionLabel('Update & Kirim Notifikasi'),

            // Action untuk approve/reject dengan satu klik
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
        ];
    }

    /**
     * Send status update notification to the user who submitted the request
     */
    protected function sendStatusUpdateNotification(string $oldStatus = null, string $newStatus = null, string $catatan = null): void
    {
        $permohonan = $this->record->fresh(); // Get fresh data
        $user = $permohonan->user;
        
        if (!$user) {
            return;
        }

        // Use current status if not provided
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

        // Create notification body
        $body = "Status permohonan Anda dengan kode {$permohonan->kode_permohonan} telah diperbarui menjadi: {$statusLabel}";
        
        if ($catatan ?? $permohonan->catatan_petugas) {
            $body .= "\n\nCatatan: " . ($catatan ?? $permohonan->catatan_petugas);
        }

        // Send notification to user
        Notification::make()
            ->title('Status Permohonan Diperbarui')
            ->icon($notificationIcon)
            ->body($body)
            ->color($notificationColor)
            ->sendToDatabase($user);

        // Log the activity
        activity()
            ->performedOn($permohonan)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $currentStatus,
                'catatan' => $catatan ?? $permohonan->catatan_petugas,
            ])
            ->log("Status permohonan diubah dari {$oldStatus} ke {$currentStatus}");
    }
}