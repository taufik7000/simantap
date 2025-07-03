<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use App\Models\Permohonan;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPermohonans extends ListRecords
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Aksi untuk menugaskan semua permohonan yang belum ditugaskan secara otomatis
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
                ->visible(fn () => Auth::user()->hasAnyRole(['admin', 'kadis']))
                ->requiresConfirmation()
                ->modalHeading('Auto-Assign Semua Permohonan')
                ->modalDescription('Sistem akan menugaskan semua permohonan yang belum ditugaskan ke petugas dengan workload paling ringan. Apakah Anda yakin?')
                ->modalSubmitActionLabel('Ya, Lanjutkan'),

            // Aksi untuk melihat statistik beban kerja petugas
            Actions\Action::make('workload_stats')
                ->label('Statistik Workload')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->modalContent(fn () => view('filament.petugas.modals.workload-statistics', [
                    'workloadDistribution' => Permohonan::getWorkloadDistribution(),
                    'assignmentStats' => Permohonan::getAssignmentStatistics(),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->visible(fn () => Auth::user()->hasAnyRole(['admin', 'kadis'])),
        ];
    }

 public function getTabs(): array
{
    $user = Auth::user();
    $tabs = [];

    if ($user->hasAnyRole(['admin', 'kadis'])) {
        $tabs['semua'] = Tab::make('Semua Permohonan')
            ->badge($this->getModel()::count());
    }

    // Tab untuk tugas milik petugas yang sedang login
    $tabs['tugas_saya'] = Tab::make('Tugas Saya')
        ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', $user->id))
        ->badge($this->getModel()::where('assigned_to', $user->id)->count())
        ->badgeColor('success');

    // PERUBAHAN: Tab belum ditugaskan sekarang bisa dilihat semua petugas
    $tabs['belum_ditugaskan'] = Tab::make('Belum Ditugaskan')
        ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('assigned_to'))
        ->badge($this->getModel()::whereNull('assigned_to')->count())
        ->badgeColor('danger');
    
    // Tab overdue assignment - hanya untuk admin/kadis
    if ($user->hasAnyRole(['admin', 'kadis'])) {
        $overdueCount = $this->getModel()::whereNotNull('assigned_at')
            ->where('assigned_at', '<', now()->subHours(72))
            ->whereNotIn('status', ['selesai', 'ditolak'])->count();
        if ($overdueCount > 0) {
            $tabs['overdue'] = Tab::make('Assignment Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('assigned_at')
                    ->where('assigned_at', '<', now()->subHours(72))
                    ->whereNotIn('status', ['selesai', 'ditolak']))
                ->badge($overdueCount)
                ->badgeColor('danger')
                ->icon('heroicon-o-exclamation-triangle');
        }
    }
    
    return $tabs;
    }

    protected function getHeaderWidgets(): array
    {
        // Menampilkan widget yang berbeda berdasarkan peran
        if (Auth::user()->hasAnyRole(['admin', 'kadis'])) {
            return [
                \App\Filament\Petugas\Widgets\AssignmentStatsWidget::class,
            ];
        }

        return [
            \App\Filament\Petugas\Widgets\MyWorkloadWidget::class,
        ];
    }

    public function getDefaultActiveTab(): string
    {
        $user = Auth::user();
        
        if ($user->hasAnyRole(['kadis'])) {
            // Admin/kadis melihat yang belum ditugaskan terlebih dahulu
            return 'belum_ditugaskan';
        }
        
        // Kadis biasa melihat belum ditugaskan terlebih dahulu
        return 'belum_ditugaskan';
    }
}