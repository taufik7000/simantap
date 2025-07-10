<?php

namespace App\Filament\Kadis\Resources\PersetujuanResource\Pages;

use App\Filament\Kadis\Resources\PersetujuanResource;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPersetujuan extends ViewRecord
{
    protected static string $resource = PersetujuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui Permohonan')
                ->color('success')->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function () {
                    // Mengubah status DAN catatan petugas
                    $this->record->update([
                        'status' => 'disetujui',
                        'catatan_petugas' => 'Permohonan telah diperiksa dan disetujui oleh Kepala Dinas. Dokumen dalam antrian penerbitan.',
                    ]);
                    Notification::make()->title('Permohonan Disetujui')->success()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Action::make('reject')
                ->label('Tolak Permohonan')
                ->color('danger')->icon('heroicon-o-x-circle')
                ->form([
                    Forms\Components\Textarea::make('catatan_petugas')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'ditolak',
                        'catatan_petugas' => $data['catatan_petugas'],
                    ]);
                    Notification::make()->title('Permohonan Ditolak')->warning()->send();
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