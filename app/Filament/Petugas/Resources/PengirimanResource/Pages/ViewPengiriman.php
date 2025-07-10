<?php

namespace App\Filament\Petugas\Resources\PengirimanResource\Pages;

use App\Filament\Petugas\Resources\PengirimanResource;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPengiriman extends ViewRecord
{
    protected static string $resource = PengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol baru untuk mengirim dokumen digital
            Action::make('send_digital_document')
                ->label('Unggah & Kirim Dokumen Digital')
                ->color('success')->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('dokumen_digital_path')
                        ->label('File Dokumen Digital (PDF)')
                        ->disk('private')
                        ->directory('dokumen-terbitan')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required(),
                    Forms\Components\Textarea::make('catatan_petugas')
                        ->label('Catatan Tambahan untuk Warga')
                        ->placeholder('Contoh: Silakan unduh dokumen Anda dari akun Anda.')
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'selesai',
                        'dokumen_digital_path' => $data['dokumen_digital_path'],
                        'catatan_petugas' => $data['catatan_petugas'] ?: 'Dokumen Anda telah diterbitkan secara digital dan siap untuk diunduh.',
                    ]);
                    Notification::make()->title('Dokumen Digital Berhasil Dikirim')->success()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->modalHeading('Kirim Dokumen Digital ke Warga'),

            // Tombol untuk menandai pengiriman fisik
            Action::make('start_physical_delivery')
                ->label('Kirim Dokumen Fisik')
                ->color('info')->icon('heroicon-o-truck')
                ->requiresConfirmation()
                ->modalDescription('Ubah status menjadi "Proses Pengiriman" untuk dokumen yang dikirim ke kantor camat.')
                ->action(function () {
                    $this->record->update([
                        'status' => 'proses_pengiriman',
                        'catatan_petugas' => 'Dokumen sedang dalam proses pengiriman ke lokasi pengambilan.',
                    ]);
                    Notification::make()->title('Pengiriman Fisik Dimulai')->success()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $viewPermohonanPage = new \App\Filament\Petugas\Resources\PermohonanResource\Pages\ViewPermohonan();
        return $viewPermohonanPage->infolist($infolist);
    }
}