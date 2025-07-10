<?php

namespace App\Filament\Kadis\Widgets;

use App\Models\Permohonan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Mengambil data statistik langsung dari model Permohonan
        $stats = Permohonan::getAssignmentStatistics();

        return [
            Stat::make('Total Semua Permohonan', $stats['total_permohonan'])
                ->description('Jumlah seluruh permohonan yang masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Menunggu Ditugaskan', $stats['belum_ditugaskan'])
                ->description('Permohonan baru yang perlu ditugaskan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($stats['belum_ditugaskan'] > 0 ? 'danger' : 'success'),

            Stat::make('Tugas Overdue', $stats['overdue_assignment'])
                ->description('Penugasan yang terlambat (> 72 jam)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stats['overdue_assignment'] > 0 ? 'danger' : 'success'),
        ];
    }
}