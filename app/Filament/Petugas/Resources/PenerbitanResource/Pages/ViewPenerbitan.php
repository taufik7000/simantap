<?php

namespace App\Filament\Petugas\Resources\PenerbitanResource\Pages;

use App\Filament\Petugas\Resources\PenerbitanResource;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPenerbitan extends ViewRecord
{
    protected static string $resource = PenerbitanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish_document')
                ->label('Dokumen Diterbitkan')
                ->color('success')->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalDescription('Pastikan dokumen sudah dicetak atau siap dikirim secara digital.')
                ->action(function () {
                    $this->record->update(['status' => 'dokumen_diterbitkan']);
                    Notification::make()->title('Dokumen Telah Diterbitkan')->success()->send();
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