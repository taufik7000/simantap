<?php

namespace App\Filament\Petugas\Widgets;

use App\Models\Permohonan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssignmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = Permohonan::getAssignmentStatistics();
        $workloadDistribution = Permohonan::getWorkloadDistribution();
        
        $totalPetugas = count($workloadDistribution);
        $totalActivePermohonan = collect($workloadDistribution)->sum('active_count');
        $avgWorkload = $totalPetugas > 0 ? round($totalActivePermohonan / $totalPetugas, 1) : 0;
        
        $workloadCollection = collect($workloadDistribution);
        $maxWorkload = $workloadCollection->max('active_count');
        $minWorkload = $workloadCollection->min('active_count');
        $busyPetugas = $workloadCollection->firstWhere('active_count', $maxWorkload);
        $freePetugas = $workloadCollection->firstWhere('active_count', $minWorkload);

        return [
            Stat::make('Total Permohonan', $stats['total_permohonan'])
                ->description('Semua permohonan yang masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Belum Ditugaskan', $stats['belum_ditugaskan'])
                ->description('Permohonan yang perlu ditugaskan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($stats['belum_ditugaskan'] > 0 ? 'danger' : 'success')
                ->chart($this->getUnassignedTrend()),

            Stat::make('Assignment Overdue', $stats['overdue_assignment'])
                ->description('Lebih dari 72 jam')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stats['overdue_assignment'] > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getUnassignedTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = Permohonan::whereDate('created_at', $date)
                ->whereNull('assigned_to')
                ->count();
            $trend[] = $count;
        }
        return $trend;
    }
}