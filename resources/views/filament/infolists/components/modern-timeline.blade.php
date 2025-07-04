{{-- resources/views/filament/infolists/components/modern-timeline.blade.php --}}
@php
    $record = $getRecord();
    
    // Build comprehensive timeline logs
    $allLogs = collect([
        [
            'id' => 'created',
            'created_at' => $record->created_at,
            'action' => 'Permohonan Diajukan',
            'description' => 'Permohonan baru telah diajukan oleh warga',
            'user' => $record->user->name,
            'type' => 'created',
            'icon' => 'heroicon-o-plus-circle',
            'color' => 'bg-blue-500',
            'priority' => 'high'
        ],
    ]);
    
    // Assignment log
    if ($record->assigned_to) {
        $allLogs->push([
            'id' => 'assigned',
            'created_at' => $record->assigned_at ?? $record->updated_at,
            'action' => 'Ditugaskan',
            'description' => 'Ditugaskan ke ' . $record->assignedTo->name,
            'user' => 'System',
            'type' => 'assigned',
            'icon' => 'heroicon-o-user-plus',
            'color' => 'bg-green-500',
            'priority' => 'high'
        ]);
    }
    
    // Status changes (from logs if available)
    $statusChanges = [
        'sedang_ditinjau' => ['label' => 'Mulai Ditinjau', 'color' => 'bg-yellow-500'],
        'verifikasi_berkas' => ['label' => 'Verifikasi Berkas', 'color' => 'bg-blue-500'],
        'diproses' => ['label' => 'Mulai Diproses', 'color' => 'bg-indigo-500'],
        'disetujui' => ['label' => 'Disetujui', 'color' => 'bg-green-500'],
        'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-red-500'],
        'selesai' => ['label' => 'Selesai', 'color' => 'bg-emerald-500'],
    ];
    
    if (isset($statusChanges[$record->status])) {
        $statusInfo = $statusChanges[$record->status];
        $allLogs->push([
            'id' => 'status_' . $record->status,
            'created_at' => $record->updated_at,
            'action' => $statusInfo['label'],
            'description' => $record->catatan_petugas ?: 'Status permohonan diperbarui',
            'user' => $record->assignedTo->name ?? 'Petugas',
            'type' => 'status_change',
            'icon' => 'heroicon-o-arrow-path',
            'color' => $statusInfo['color'],
            'priority' => 'high'
        ]);
    }
    
    // Revisions
    foreach ($record->revisions as $revision) {
        $allLogs->push([
            'id' => 'revision_sent_' . $revision->id,
            'created_at' => $revision->created_at,
            'action' => 'Revisi Ke-' . $revision->revision_number,
            'description' => 'Warga mengirim revisi dengan ' . count($revision->berkas_revisi ?? []) . ' file',
            'user' => $record->user->name,
            'type' => 'revision_sent',
            'icon' => 'heroicon-o-arrow-path',
            'color' => 'bg-orange-500',
            'priority' => $revision->status === 'pending' ? 'high' : 'medium',
            'meta' => [
                'revision_id' => $revision->id,
                'file_count' => count($revision->berkas_revisi ?? []),
                'status' => $revision->status
            ]
        ]);
        
        if ($revision->reviewed_at) {
            $allLogs->push([
                'id' => 'revision_reviewed_' . $revision->id,
                'created_at' => $revision->reviewed_at,
                'action' => $revision->status === 'accepted' ? 'Revisi Diterima' : 'Revisi Ditolak',
                'description' => $revision->catatan_petugas ?: 'Revisi telah direview',
                'user' => $revision->reviewedBy->name ?? 'Petugas',
                'type' => $revision->status === 'accepted' ? 'revision_approved' : 'revision_rejected',
                'icon' => $revision->status === 'accepted' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
                'color' => $revision->status === 'accepted' ? 'bg-green-500' : 'bg-red-500',
                'priority' => 'high'
            ]);
        }
    }
    
    // Sort by date descending and add priority sorting
    $allLogs = $allLogs->sortByDesc(function($log) {
        $priorityWeight = $log['priority'] === 'high' ? 1000 : 0;
        return $log['created_at']->timestamp + $priorityWeight;
    });
    
    $totalLogs = $allLogs->count();
    $recentLogs = $allLogs->take(3); // Show only 3 most recent
    $olderLogs = $allLogs->skip(3);
@endphp

<div class="space-y-4" data-section="timeline">
    <!-- Timeline Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <h4 class="text-sm font-semibold text-gray-900">Timeline Aktivitas</h4>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ $totalLogs }} aktivitas
            </span>
        </div>
        
        <!-- Timeline Controls -->
        <div class="flex items-center space-x-2">
            @if($olderLogs->count() > 0)
                <button onclick="toggleOlderLogs()" 
                        class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">
                    <span id="toggle-text">Lihat Semua ({{ $olderLogs->count() }} lainnya)</span>
                </button>
            @endif
            
            <button onclick="refreshTimeline()" 
                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
            </button>
        </div>
    </div>

    <!-- Modern Timeline Container -->
    <div class="relative">
        <!-- Timeline Line -->
        <div class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-gray-200 via-gray-300 to-transparent"></div>
        
        <!-- Recent Activities (Always Visible) -->
        <div class="space-y-4" id="recent-logs">
            @foreach($recentLogs as $index => $log)
                <div class="relative flex items-start group hover:bg-gray-50 rounded-lg p-2 transition-all duration-200">
                    <!-- Timeline Dot -->
                    <div class="relative z-10 flex h-12 w-12 items-center justify-center rounded-full {{ $log['color'] }} text-white shadow-lg ring-4 ring-white group-hover:scale-110 transition-transform duration-200">
                        <x-dynamic-component :component="$log['icon']" class="h-5 w-5" />
                    </div>
                    
                    <!-- Content -->
                    <div class="ml-4 flex-1 min-w-0">
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $log['action'] }}
                            </h5>
                            <div class="flex items-center space-x-2">
                                @if($log['priority'] === 'high')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Penting
                                    </span>
                                @endif
                                <time class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ $log['created_at']->format('d M, H:i') }}
                                </time>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ $log['description'] }}</p>
                        
                        <!-- Meta Info -->
                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <x-heroicon-o-user class="w-3 h-3 mr-1" />
                                {{ $log['user'] }}
                            </span>
                            <span>{{ $log['created_at']->diffForHumans() }}</span>
                            
                            @if(isset($log['meta']))
                                @if(isset($log['meta']['file_count']) && $log['meta']['file_count'] > 0)
                                    <span class="flex items-center text-blue-600">
                                        <x-heroicon-o-document class="w-3 h-3 mr-1" />
                                        {{ $log['meta']['file_count'] }} file
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <!-- Quick Actions untuk Revision -->
                        @if($log['type'] === 'revision_sent' && isset($log['meta']['status']) && $log['meta']['status'] === 'pending')
                            <div class="mt-3 flex space-x-2">
                                <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="revision_id" value="{{ $log['meta']['revision_id'] }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit"
                                            onclick="return confirm('Terima revisi ini?')"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                        <x-heroicon-o-check class="w-3 h-3 mr-1" />
                                        Terima
                                    </button>
                                </form>
                                
                                <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="revision_id" value="{{ $log['meta']['revision_id'] }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit"
                                            onclick="return confirm('Tolak revisi ini?')"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors">
                                        <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Older Activities (Collapsible) -->
        @if($olderLogs->count() > 0)
            <div id="older-logs" class="hidden space-y-3 mt-4 pt-4 border-t border-gray-200">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide px-2">
                    Aktivitas Sebelumnya
                </div>
                
                <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    @foreach($olderLogs as $log)
                        <div class="relative flex items-start py-2 px-2 hover:bg-gray-50 rounded transition-colors">
                            <!-- Smaller Timeline Dot -->
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full {{ $log['color'] }} text-white shadow-sm ring-2 ring-white">
                                <x-dynamic-component :component="$log['icon']" class="h-3 w-3" />
                            </div>
                            
                            <!-- Compact Content -->
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-900">{{ $log['action'] }}</span>
                                    <time class="text-xs text-gray-500">{{ $log['created_at']->format('d M') }}</time>
                                </div>
                                <p class="text-xs text-gray-600 mt-0.5 line-clamp-1">{{ $log['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Empty State -->
        @if($totalLogs === 0)
            <div class="text-center py-8">
                <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                <p class="text-sm text-gray-500">Belum ada aktivitas</p>
            </div>
        @endif
    </div>
    
    <!-- Timeline Summary -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 border border-blue-200">
        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Dibuat {{ $record->created_at->diffForHumans() }}</span>
                @if($record->assigned_to)
                    <span class="text-gray-600">Petugas: {{ $record->assignedTo->name }}</span>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                @php
                    $processingDays = $record->created_at->diffInDays(now());
                    $urgencyColor = $processingDays > 7 ? 'text-red-600' : ($processingDays > 3 ? 'text-yellow-600' : 'text-green-600');
                @endphp
                <span class="{{ $urgencyColor }} font-medium">
                    {{ $processingDays }} hari proses
                </span>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced scrollbar */
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 2px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 2px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Line clamp utility */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth animations */
.group:hover .timeline-dot {
    transform: scale(1.1);
}

/* Gradient timeline line */
.timeline-line {
    background: linear-gradient(to bottom, #e5e7eb, #d1d5db, transparent);
}
</style>

<script>
function toggleOlderLogs() {
    const olderLogs = document.getElementById('older-logs');
    const toggleText = document.getElementById('toggle-text');
    
    if (olderLogs.classList.contains('hidden')) {
        olderLogs.classList.remove('hidden');
        olderLogs.classList.add('animate-fadeIn');
        toggleText.textContent = 'Sembunyikan';
    } else {
        olderLogs.classList.add('hidden');
        toggleText.textContent = 'Lihat Semua ({{ $olderLogs->count() }} lainnya)';
    }
}

function refreshTimeline() {
    // Add refresh animation
    const button = event.target.closest('button');
    button.classList.add('animate-spin');
    
    // Simulate refresh (in real app, this would reload timeline data)
    setTimeout(() => {
        button.classList.remove('animate-spin');
        // window.location.reload(); // Uncomment for actual refresh
    }, 1000);
}

// Auto-expand if there are pending revisions
document.addEventListener('DOMContentLoaded', function() {
    @if($record->revisions()->where('status', 'pending')->count() > 0)
        // Auto-show older logs if there are pending items
        const olderLogs = document.getElementById('older-logs');
        if (olderLogs && olderLogs.querySelector('[data-status="pending"]')) {
            toggleOlderLogs();
        }
    @endif
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>