<?php

namespace App\Filament\Petugas\Resources\EntriDataResource\Pages;

use App\Filament\Petugas\Resources\EntriDataResource;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEntriData extends ViewRecord
{
    protected static string $resource = EntriDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete_entry')
                ->label('Entri Data Selesai')
                ->color('success')->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalDescription('Pastikan semua data sudah diinput dengan benar sebelum melanjutkan.')
                ->action(function () {
                    $this->record->update([
                        'status' => 'menunggu_persetujuan',
                        'catatan_petugas' => 'Proses entri data telah selesai dan sekarang menunggu persetujuan akhir.',
                    ]);
                    Notification::make()->title('Entri Data Selesai')->success()->send();
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