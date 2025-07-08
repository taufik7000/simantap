{{-- resources/views/filament/infolists/components/modern-timeline.blade.php --}}
@php
    $record = $getRecord();
    
    // Logika untuk mengumpulkan semua log tetap sama
    $allLogs = collect();
    
    // 1. Log Permohonan Dibuat
    $allLogs->push([
        'id' => 'created',
        'created_at' => $record->created_at,
        'action' => 'Permohonan Diajukan',
        'description' => 'Permohonan baru telah diajukan oleh warga.',
        'user' => $record->user->name,
        'icon' => 'heroicon-o-plus-circle',
        'color' => 'bg-blue-500',
    ]);

    // 2. Log dari Tabel 'permohonan_logs'
    foreach ($record->logs as $log) {
        if ($log->status === 'baru' && $log->user_id === $record->user_id) continue;
        
        $isRevision = in_array($log->status, ['butuh_perbaikan', 'membutuhkan_revisi', 'ditolak']);
        $isSuccess = in_array($log->status, ['disetujui', 'selesai', 'dokumen_diterbitkan']);
        $logColor = $isRevision ? 'bg-red-500' : ($isSuccess ? 'bg-green-500' : 'bg-indigo-500');
        $logIcon = $isRevision ? 'heroicon-o-exclamation-triangle' : ($isSuccess ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-path');
        
        $allLogs->push([
            'id' => 'log_' . $log->id,
            'created_at' => $log->created_at,
            'action' => \App\Models\Permohonan::STATUS_OPTIONS[$log->status] ?? Str::title(str_replace('_', ' ', $log->status)),
            'description' => $log->catatan ?: 'Status permohonan diperbarui.',
            'user' => $log->user->name ?? 'Sistem',
            'icon' => $logIcon,
            'color' => $logColor,
        ]);
    }

    // 3. Log Revisi dari Warga
    foreach ($record->revisions as $revision) {
        $allLogs->push([
            'id' => 'revision_sent_' . $revision->id,
            'created_at' => $revision->created_at,
            'action' => 'Warga Mengirim Perbaikan (Revisi ke-'.$revision->revision_number.')',
            'description' => $revision->catatan_revisi ?: 'Warga mengirimkan dokumen perbaikan.',
            'user' => $revision->user->name,
            'icon' => 'heroicon-o-paper-airplane',
            'color' => 'bg-orange-500',
        ]);
        
        if ($revision->reviewed_at) {
            $isAccepted = $revision->status === 'accepted';
            $allLogs->push([
                'id' => 'revision_reviewed_' . $revision->id,
                'created_at' => $revision->reviewed_at,
                'action' => $isAccepted ? 'Revisi Diterima' : 'Revisi Ditolak',
                'description' => $revision->catatan_petugas ?: 'Revisi telah direview oleh petugas.',
                'user' => $revision->reviewedBy->name ?? 'Petugas',
                'icon' => $isAccepted ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
                'color' => $isAccepted ? 'bg-green-500' : 'bg-red-500',
            ]);
        }
    }
    
    $allLogs = $allLogs->unique('id')->sortByDesc('created_at');
    $totalLogs = $allLogs->count();
@endphp

<div class="space-y-4" data-section="timeline">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <h4 class="text-sm font-semibold text-gray-900">Riwayat Lengkap Aktivitas</h4>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ $totalLogs }} aktivitas
            </span>
        </div>
    </div>

    <div class="relative max-h-96 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <div class="absolute left-6 top-0 bottom-0 w-px bg-gray-200"></div>
        
        <div class="space-y-4">
            @forelse($allLogs as $log)
                <div class="relative flex items-start group">
                    <div class="relative z-10 flex h-12 w-12 items-center justify-center rounded-full {{ $log['color'] }} text-white shadow-lg ring-4 ring-white">
                        <x-dynamic-component :component="$log['icon']" class="h-5 w-5" />
                    </div>
                    
                    <div class="ml-4 flex-1 min-w-0">
                        <h5 class="text-sm font-semibold text-gray-900">
                            {{ $log['action'] }}
                        </h5>
                        <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ $log['description'] }}</p>
                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <x-heroicon-o-user class="w-3 h-3 mr-1" />
                                {{ $log['user'] }}
                            </span>
                            <span>{{ $log['created_at']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                    <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.scrollbar-thin::-webkit-scrollbar {
    width: 5px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f5f9; /* bg-slate-100 */
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #cbd5e1; /* bg-slate-300 */
    border-radius: 10px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; /* bg-slate-400 */
}
</style>