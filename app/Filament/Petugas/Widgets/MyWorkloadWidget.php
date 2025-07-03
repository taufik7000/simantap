<?php

namespace App\Filament\Petugas\Widgets;

use App\Models\Permohonan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyWorkloadWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $userId = Auth::id();

        // --- PERBAIKAN DI SEMUA BARIS INI ---
        $myActive = Permohonan::where('assigned_to', $userId)
            ->whereNotIn('status', ['selesai', 'ditolak', 'disetujui'])
            ->count();
            
        $myCompleted = Permohonan::where('assigned_to', $userId)
            ->whereIn('status', ['selesai', 'disetujui'])
            ->count();
            
        $myTotal = Permohonan::where('assigned_to', $userId)->count();
        
        $completionRate = $myTotal > 0 ? round(($myCompleted / $myTotal) * 100, 1) : 0;
        
        $needsAttention = Permohonan::where('assigned_to', $userId)
            ->whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan'])
            ->count();
            
        $avgResolutionDays = Permohonan::where('assigned_to', $userId)
            ->whereIn('status', ['selesai', 'disetujui'])
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG(DATEDIFF(updated_at, assigned_at)) as avg_days')
            ->value('avg_days');
        $avgResolutionDays = $avgResolutionDays ? round($avgResolutionDays, 1) : 0;

        return [
            Stat::make('Tugas Aktif Saya', $myActive)
                ->description('Permohonan yang sedang ditangani')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color($myActive > 10 ? 'warning' : 'primary')
                ->chart($this->getMyWorkloadTrend()),

            Stat::make('Selesai Dikerjakan', $myCompleted)
                ->description('Total permohonan selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Tingkat Penyelesaian', $completionRate . '%')
                ->description('Dari total tugas yang diterima')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Perlu Perhatian', $needsAttention)
                ->description('Membutuhkan revisi/perbaikan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($needsAttention > 0 ? 'danger' : 'success'),

            Stat::make('Rata-rata Penyelesaian', $avgResolutionDays . ' hari')
                ->description('Waktu rata-rata menyelesaikan tugas')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgResolutionDays <= 3 ? 'success' : ($avgResolutionDays <= 7 ? 'warning' : 'danger')),
        ];
    }

    protected function getMyWorkloadTrend(): array
    {
        $userId = Auth::id();
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            // --- PERBAIKAN DI SINI ---
            $count = Permohonan::where('assigned_to', $userId)
                ->whereDate('assigned_at', $date)
                ->count();
            $trend[] = $count;
        }
        
        return $trend;
    }
}