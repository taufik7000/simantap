<?php
namespace App\Filament\Kadis\Resources\PermohonanResource\Pages;
use App\Filament\Kadis\Resources\PermohonanResource;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;
    // Menggunakan Infolist dari PetugasResource agar tampilan detail konsisten
    public function infolist(Infolist $infolist): Infolist
    {
        $viewPermohonanPetugas = new \App\Filament\Petugas\Resources\PermohonanResource\Pages\ViewPermohonan();
        return $viewPermohonanPetugas->infolist($infolist);
    }
}