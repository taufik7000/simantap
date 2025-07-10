<?php

namespace App\Filament\Kadis\Resources\PermohonanResource\Pages;

use App\Filament\Kadis\Resources\PermohonanResource;
use App\Models\Permohonan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonans extends ListRecords
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('auto_assign_all')
                ->label('Auto-Assign Semua')
                ->icon('heroicon-o-cpu-chip')
                ->color('warning')
                ->action(function () {
                    $unassignedPermohonans = Permohonan::whereNull('assigned_to')
                        ->whereNotIn('status', ['selesai', 'ditolak'])
                        ->get();

                    $successCount = 0;
                    foreach ($unassignedPermohonans as $permohonan) {
                        if ($permohonan->autoAssign()) {
                            $successCount++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("Auto-Assignment Selesai")
                        ->body("Berhasil menugaskan {$successCount} dari {$unassignedPermohonans->count()} permohonan.")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Auto-Assign Semua Permohonan')
                ->modalDescription('Sistem akan menugaskan semua permohonan yang belum ditugaskan ke petugas dengan workload paling ringan. Apakah Anda yakin?')
                ->modalSubmitActionLabel('Ya, Lanjutkan'),

            // Tombol untuk menampilkan modal statistik workload
            Actions\Action::make('workload_stats')
                ->label('Statistik Workload')
                ->icon('heroicon-o-chart-bar-square')
                ->color('gray')
                ->modalContent(fn () => view('filament.petugas.modals.workload-statistics', [
                    'workloadDistribution' => Permohonan::getWorkloadDistribution(),
                    'assignmentStats' => Permohonan::getAssignmentStatistics(),
                ]))
                ->modalSubmitAction(false) // Sembunyikan tombol submit
                ->modalCancelActionLabel('Tutup'), // Ubah label tombol batal

            // Anda bisa menambahkan aksi lain untuk Kadis di sini jika perlu
        ];
    }
}