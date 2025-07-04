{{-- resources/views/filament/infolists/components/paginated-timeline.blade.php --}}
@php
    $record = $getRecord();
    $perPage = 5; // Tampilkan 5 log per halaman
    $page = request()->get('timeline_page', 1);
    
    // Simulasi timeline logs (ganti dengan data real dari model)
    $allLogs = collect([
        [
            'created_at' => $record->created_at,
            'action' => 'Permohonan diajukan',
            'description' => 'Permohonan baru telah diajukan oleh ' . $record->user->name,
            'user' => $record->user->name,
            'type' => 'created',
            'icon' => 'heroicon-o-plus-circle',
            'color' => 'bg-blue-100 text-blue-600'
        ],
    ]);
    
    // Tambahkan log assignment jika ada
    if ($record->assigned_to) {
        $allLogs->push([
            'created_at' => $record->updated_at,
            'action' => 'Permohonan ditugaskan',
            'description' => 'Permohonan ditugaskan ke ' . $record->assignedTo->name,
            'user' => 'System',
            'type' => 'assigned',
            'icon' => 'heroicon-o-user-plus',
            'color' => 'bg-green-100 text-green-600'
        ]);
    }
    
    // Tambahkan log dari revisi
    foreach ($record->revisions as $revision) {
        $allLogs->push([
            'created_at' => $revision->created_at,
            'action' => 'Revisi ke-' . $revision->revision_number . ' dikirim',
            'description' => 'Warga mengirimkan revisi dengan ' . count($revision->berkas_revisi ?? []) . ' file',
            'user' => $record->user->name,
            'type' => 'revision_sent',
            'icon' => 'heroicon-o-arrow-path',
            'color' => 'bg-yellow-100 text-yellow-600'
        ]);
        
        if ($revision->reviewed_at) {
            $allLogs->push([
                'created_at' => $revision->reviewed_at,
                'action' => 'Revisi ' . ($revision->status === 'accepted' ? 'diterima' : 'ditolak'),
                'description' => $revision->catatan_petugas ?: 'Revisi telah direview oleh petugas',
                'user' => $revision->reviewedBy->name ?? 'Petugas',
                'type' => $revision->status === 'accepted' ? 'revision_approved' : 'revision_rejected',
                'icon' => $revision->status === 'accepted' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
                'color' => $revision->status === 'accepted' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
            ]);
        }
    }
    
    // Sort by date descending
    $allLogs = $allLogs->sortByDesc('created_at');
    
    // Paginate
    $totalLogs = $allLogs->count();
    $totalPages = ceil($totalLogs / $perPage);
    $offset = ($page - 1) * $perPage;
    $logs = $allLogs->slice($offset, $perPage);
@endphp

<div class="space-y-4" data-section="timeline">
    <!-- Timeline Container dengan max height -->
    <div class="max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        @if($logs->count() > 0)
            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <div class="space-y-4">
                    @foreach($logs as $index => $log)
                        <div class="relative flex items-start space-x-3">
                            <!-- Timeline dot -->
                            <div class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $log['color'] }} ring-8 ring-white">
                                <x-dynamic-component :component="$log['icon']" class="h-4 w-4" />
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">{{ $log['action'] }}</div>
                                    <div class="text-gray-500 mt-1">{{ $log['description'] }}</div>
                                </div>
                                <div class="mt-2 flex items-center space-x-2 text-xs text-gray-400">
                                    <span>{{ $log['user'] }}</span>
                                    <span>•</span>
                                    <time datetime="{{ $log['created_at']->toISOString() }}">
                                        {{ $log['created_at']->format('d M Y, H:i') }}
                                    </time>
                                    <span>•</span>
                                    <span>{{ $log['created_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <x-heroicon-o-clock class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                <p>Belum ada aktivitas</p>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                @if($page > 1)
                    <button onclick="loadTimelinePage({{ $page - 1 }})" 
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </button>
                @endif
                
                @if($page < $totalPages)
                    <button onclick="loadTimelinePage({{ $page + 1 }})" 
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Next
                    </button>
                @endif
            </div>
            
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $offset + 1 }}</span>
                        sampai
                        <span class="font-medium">{{ min($offset + $perPage, $totalLogs) }}</span>
                        dari
                        <span class="font-medium">{{ $totalLogs }}</span>
                        aktivitas
                    </p>
                </div>
                
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        @if($page > 1)
                            <button onclick="loadTimelinePage({{ $page - 1 }})" 
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Previous</span>
                                <x-heroicon-o-chevron-left class="h-5 w-5" />
                            </button>
                        @endif
                        
                        @for($i = 1; $i <= $totalPages; $i++)
                            <button onclick="loadTimelinePage({{ $i }})" 
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold {{ $i == $page ? 'bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' }}">
                                {{ $i }}
                            </button>
                        @endfor
                        
                        @if($page < $totalPages)
                            <button onclick="loadTimelinePage({{ $page + 1 }})" 
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Next</span>
                                <x-heroicon-o-chevron-right class="h-5 w-5" />
                            </button>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function loadTimelinePage(page) {
    // Reload page with timeline_page parameter
    const url = new URL(window.location);
    url.searchParams.set('timeline_page', page);
    window.location.href = url.toString();
}
</script>