<?php

namespace App\Filament\Petugas\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Petugas\Resources\PermohonanRevisionResource;
use App\Models\PermohonanRevision;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPermohonanRevision extends EditRecord
{
    protected static string $resource = PermohonanRevisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make(),
            
            // Quick action buttons di header
            \Filament\Actions\Action::make('quick_approve')
                ->label('Terima Revisi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Terima Revisi')
                ->modalDescription('Apakah Anda yakin ingin menerima revisi ini?')
                ->action(function () {
                    $this->record->update([
                        'status' => 'accepted',
                        'catatan_petugas' => 'Revisi diterima dan akan diproses lebih lanjut.',
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    $this->updatePermohonanStatus('accepted');

                    Notification::make()
                        ->title('Revisi Diterima')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'pending'),

            \Filament\Actions\Action::make('quick_reject')
                ->label('Tolak Revisi')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('catatan_petugas')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Jelaskan mengapa revisi ini ditolak...'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'catatan_petugas' => $data['catatan_petugas'],
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    $this->updatePermohonanStatus('rejected', $data['catatan_petugas']);

                    Notification::make()
                        ->title('Revisi Ditolak')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'pending'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set reviewed timestamp dan reviewer jika status berubah dari pending
        if (isset($data['status']) && $data['status'] !== 'pending' && $this->record->status === 'pending') {
            $data['reviewed_at'] = now();
            $data['reviewed_by'] = Auth::id();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $revision = $this->record;
        $permohonan = $revision->permohonan;

        // Update status permohonan berdasarkan status revisi
        $this->updatePermohonanStatus($revision->status, $revision->catatan_petugas);

        // Kirim notifikasi ke warga
        $this->sendNotificationToUser();
    }

    protected function updatePermohonanStatus(string $revisionStatus, ?string $catatan = null): void
    {
        $revision = $this->record;
        $permohonan = $revision->permohonan;

        match ($revisionStatus) {
            'accepted' => $permohonan->update([
                'status' => 'diproses',
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} diterima. Permohonan akan diproses lebih lanjut.",
            ]),
            'rejected' => $permohonan->update([
                'status' => 'membutuhkan_revisi',
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} ditolak. " . ($catatan ?? $revision->catatan_petugas),
            ]),
            'reviewed' => $permohonan->update([
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} telah direview. " . ($catatan ?? $revision->catatan_petugas),
            ]),
            default => null,
        };
    }

    protected function sendNotificationToUser(): void
    {
        $revision = $this->record;
        $permohonan = $revision->permohonan;

        $statusLabels = [
            'pending' => 'Menunggu Review',
            'reviewed' => 'Sudah Direview',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];

        $statusLabel = $statusLabels[$revision->status] ?? $revision->status;
        $notificationColor = match($revision->status) {
            'accepted' => 'success',
            'rejected' => 'danger',
            'reviewed' => 'info',
            default => 'warning',
        };

        $body = "Revisi ke-{$revision->revision_number} untuk permohonan {$permohonan->kode_permohonan} telah {$statusLabel}.";
        
        if ($revision->catatan_petugas) {
            $body .= "\n\nCatatan: " . $revision->catatan_petugas;
        }

        Notification::make()
            ->title('Status Revisi Diperbarui')
            ->icon('heroicon-o-arrow-path')
            ->body($body)
            ->color($notificationColor)
            ->sendToDatabase($revision->user);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Review revisi berhasil disimpan';
    }

    // Override method ini untuk memastikan form bisa disimpan
    protected function authorizeAccess(): void
    {
        // Petugas bisa edit semua revisi
        abort_unless(auth()->user()->hasRole(['petugas', 'admin', 'kadis']), 403);
    }
}

// File: app/Filament/Petugas/Resources/PermohonanRevisionResource/Pages/ViewPermohonanRevision.php

namespace App\Filament\Petugas\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Petugas\Resources\PermohonanRevisionResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewPermohonanRevision extends ViewRecord
{
    protected static string $resource = PermohonanRevisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Review Revisi')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'pending'),

            Actions\Action::make('quick_approve')
                ->label('Terima')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Terima Revisi')
                ->modalDescription('Apakah Anda yakin ingin menerima revisi ini?')
                ->action(function () {
                    $this->record->update([
                        'status' => 'accepted',
                        'catatan_petugas' => 'Revisi diterima dan akan diproses lebih lanjut.',
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    // Update permohonan status
                    $this->record->permohonan->update([
                        'status' => 'diproses',
                        'catatan_petugas' => "Revisi ke-{$this->record->revision_number} diterima. Permohonan akan diproses lebih lanjut.",
                    ]);

                    // Send notification
                    Notification::make()
                        ->title('Revisi Anda Telah Diterima')
                        ->body("Revisi ke-{$this->record->revision_number} untuk permohonan {$this->record->permohonan->kode_permohonan} telah diterima.")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Revisi Diterima')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'pending'),

            Actions\Action::make('quick_reject')
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
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'catatan_petugas' => $data['catatan_petugas'],
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    // Update permohonan status
                    $this->record->permohonan->update([
                        'status' => 'membutuhkan_revisi',
                        'catatan_petugas' => "Revisi ke-{$this->record->revision_number} ditolak. " . $data['catatan_petugas'],
                    ]);

                    // Send notification
                    Notification::make()
                        ->title('Revisi Perlu Diperbaiki')
                        ->body("Revisi ke-{$this->record->revision_number} untuk permohonan {$this->record->permohonan->kode_permohonan} perlu diperbaiki.")
                        ->warning()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Revisi Ditolak')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'pending'),
        ];
    }
}