<?php

namespace App\Filament\Petugas\Resources\VerifikasiBerkasResource\Pages;

use App\Filament\Petugas\Resources\VerifikasiBerkasResource;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;
use App\Models\Permohonan;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewVerifikasi extends ViewRecord
{
    protected static string $resource = VerifikasiBerkasResource::class;

    // Definisikan hanya 2 tombol aksi yang kita butuhkan
    protected function getHeaderActions(): array
    {
        $recordStatus = $this->record->status;

        // Aksi-aksi ini hanya akan muncul jika statusnya 'verifikasi_berkas'
        $initialVerificationActions = [
            Action::make('approve_verification')
                ->label('Berkas Sudah Diverifikasi')
                ->color('success')->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'diproses',
                        'catatan_petugas' => 'Berkas telah diverifikasi dan permohonan sedang dalam proses pengerjaan.',
                    ]);
                    Notification::make()->title('Verifikasi Berhasil')->success()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible($recordStatus === 'verifikasi_berkas'),

            Action::make('request_initial_revision')
                ->label('Butuh Perbaikan')
                ->color('danger')->icon('heroicon-o-exclamation-triangle')
                ->form([Textarea::make('catatan_petugas')->label('Catatan Perbaikan')->required()->rows(5),])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'butuh_perbaikan', 'catatan_petugas' => $data['catatan_petugas']]);
                    Notification::make()->title('Permohonan dikembalikan ke warga')->warning()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible($recordStatus === 'verifikasi_berkas'),
        ];

        $revisionReviewActions = [
            Action::make('approve_revision')
                ->label('Terima Revisi')
                ->color('success')->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'verifikasi_berkas',
                        'catatan_petugas' => 'Revisi diterima. Berkas akan diverifikasi ulang oleh petugas.',
                    ]);
                    Notification::make()->title('Revisi Diterima')->success()->send();
                    $this->refreshRecord(); 
                })
                ->visible($recordStatus === 'diperbaiki_warga'),

            Action::make('reject_revision')
                ->label('Tolak Revisi (Lagi)')
                ->color('danger')->icon('heroicon-o-x-circle')
                ->form([Textarea::make('catatan_petugas')->label('Alasan Penolakan Baru')->required()->rows(5),])
                ->action(function (array $data) {
                    $this->record->update(['status' => 'butuh_perbaikan', 'catatan_petugas' => 'Revisi kembali ditolak. Alasan: ' . $data['catatan_petugas']]);
                    Notification::make()->title('Revisi Kembali Ditolak')->warning()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible($recordStatus === 'diperbaiki_warga'),
        ];

        return array_merge($initialVerificationActions, $revisionReviewActions);
    }

    // Kita gunakan Infolist dari resource Permohonan utama agar tampilannya konsisten
    public function infolist(Infolist $infolist): Infolist
    {
        // Ambil instance dari halaman ViewPermohonan untuk memanggil method infolist-nya
        $viewPermohonanPage = new \App\Filament\Petugas\Resources\PermohonanResource\Pages\ViewPermohonan();
        return $viewPermohonanPage->infolist($infolist);
    }
}