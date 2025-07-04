@php
    $record = $getRecord();
@endphp

<div class="grid grid-cols-12 gap-6">
    <!-- KOLOM KIRI - STICKY (8/12 = 67%) -->
    <div class="col-span-8">
        <div class="sticky top-4 space-y-6">
            @include('filament.infolists.components.sticky-left-column')
        </div>
    </div>

    <!-- KOLOM KANAN - SCROLLABLE (4/12 = 33%) -->
    <div class="col-span-4 space-y-6">
        <!-- STATISTIK PERMOHONAN -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center mb-4">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-500 mr-2" />
                <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <x-heroicon-s-clock class="w-5 h-5 text-gray-400 mr-2" />
                        <span class="text-sm font-medium text-gray-700">Waktu Proses</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">
                        @php
                            $hours = now()->diffInHours($record->created_at);
                            $days = floor($hours / 24);
                            $remainingHours = $hours % 24;
                        @endphp
                        @if($days > 0)
                            {{ $days }} hari {{ $remainingHours }} jam
                        @else
                            {{ $hours }} jam
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <x-heroicon-s-arrow-path class="w-5 h-5 text-gray-400 mr-2" />
                        <span class="text-sm font-medium text-gray-700">Jumlah Revisi</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->revisions()->count() > 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                        {{ $record->revisions()->count() }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <x-heroicon-s-arrow-path class="w-5 h-5 text-gray-400 mr-2" />
                        <span class="text-sm font-medium text-gray-700">Aktivitas Terakhir</span>
                    </div>
                    <span class="text-sm text-gray-600">
                        @php
                            $lastRevision = $record->revisions()->latest()->first();
                        @endphp
                        @if($lastRevision)
                            {{ $lastRevision->created_at->diffForHumans() }}
                        @else
                            {{ $record->updated_at->diffForHumans() }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- BERKAS PERMOHONAN AWAL -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <button onclick="toggleSection('berkas-awal')" class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 text-gray-500 mr-2" />
                        <h3 class="text-lg font-semibold text-gray-900">Berkas Permohonan Awal</h3>
                    </div>
                    <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform" id="berkas-awal-chevron" />
                </button>
            </div>
            
            <div id="berkas-awal-content" class="p-4 space-y-3">
                @if(is_array($record->berkas_pemohon) && count($record->berkas_pemohon) > 0)
                    @foreach($record->berkas_pemohon as $index => $berkas)
                        @if(!empty($berkas['path_dokumen']))
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <x-heroicon-o-document class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $berkas['nama_dokumen'] ?? 'Dokumen ' . ($index + 1) }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if(\Storage::disk('private')->exists($berkas['path_dokumen']))
                                                {{ \Illuminate\Support\Number::fileSize(\Storage::disk('private')->size($berkas['path_dokumen'])) }}
                                            @else
                                                Ukuran tidak diketahui
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('secure.download', ['permohonan_id' => $record->id, 'path' => $berkas['path_dokumen']]) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                                    Unduh
                                </a>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada berkas yang diupload.</p>
                @endif
            </div>
        </div>

        <!-- DETAIL REVISI TERBARU -->
        @if($record->revisions()->count() > 0)
            @php $latestRevision = $record->revisions()->latest()->first(); @endphp
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <button onclick="toggleSection('revisi-terbaru')" class="flex items-center justify-between w-full text-left">
                        <div class="flex items-center">
                            <x-heroicon-o-document-plus class="w-5 h-5 text-gray-500 mr-2" />
                            <h3 class="text-lg font-semibold text-gray-900">Revisi Terbaru</h3>
                        </div>
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform rotate-180" id="revisi-terbaru-chevron" />
                    </button>
                </div>
                
                <div id="revisi-terbaru-content" class="hidden p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <span class="text-xs font-medium text-gray-500">Revisi ke-</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $latestRevision->revision_number }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500">Status</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $latestRevision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($latestRevision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                @if($latestRevision->status === 'pending')
                                    Menunggu Review
                                @elseif($latestRevision->status === 'accepted')
                                    Diterima
                                @else
                                    Ditolak
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    @if($latestRevision->catatan_revisi)
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Catatan Warga:</p>
                            <p class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($latestRevision->catatan_revisi, 100) }}</p>
                        </div>
                    @endif
                    
                    @if(is_array($latestRevision->berkas_revisi) && count($latestRevision->berkas_revisi) > 0)
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Berkas Revisi:</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ count($latestRevision->berkas_revisi) }} file diupload
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- QUICK ACTIONS -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <button onclick="toggleSection('quick-actions')" class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center">
                        <x-heroicon-o-bolt class="w-5 h-5 text-gray-500 mr-2" />
                        <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                    </div>
                    <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform" id="quick-actions-chevron" />
                </button>
            </div>
            
            <div id="quick-actions-content" class="p-4">
                @include('filament.infolists.components.quick-actions')
            </div>
        </div>

        <!-- TIMELINE MODERN -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <button onclick="toggleSection('timeline')" class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center">
                        <x-heroicon-o-clock class="w-5 h-5 text-gray-500 mr-2" />
                        <h3 class="text-lg font-semibold text-gray-900">Timeline Permohonan</h3>
                    </div>
                    <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform rotate-180" id="timeline-chevron" />
                </button>
            </div>
            
            <div id="timeline-content" class="hidden p-4">
                @include('filament.infolists.components.modern-timeline')
            </div>
        </div>

        <!-- SEMUA REVISI -->
        @if($record->revisions()->count() > 0)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200">
                    <button onclick="toggleSection('all-revisions')" class="flex items-center justify-between w-full text-left">
                        <div class="flex items-center">
                            <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-500 mr-2" />
                            <h3 class="text-lg font-semibold text-gray-900">Semua Revisi</h3>
                        </div>
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transform transition-transform rotate-180" id="all-revisions-chevron" />
                    </button>
                </div>
                
                <div id="all-revisions-content" class="hidden p-4">
                    @include('filament.infolists.components.paginated-revisions')
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Sticky positioning untuk kolom kiri */
.sticky {
    position: sticky;
    top: 1rem;
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

/* Custom scrollbar untuk sticky column */
.sticky::-webkit-scrollbar {
    width: 6px;
}

.sticky::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.sticky::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.sticky::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Smooth transitions untuk collapsible sections */
.transform {
    transition: transform 0.2s ease-in-out;
}

.rotate-180 {
    transform: rotate(180deg);
}

/* Ensure proper spacing */
.space-y-6 > * + * {
    margin-top: 1.5rem;
}
</style>

<script>
function toggleSection(sectionId) {
    const content = document.getElementById(sectionId + '-content');
    const chevron = document.getElementById(sectionId + '-chevron');
    
    if (content && chevron) {
        if (content.classList.contains('hidden')) {
            // Show content
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            // Hide content
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }
}

// Auto-expand important sections on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand berkas awal
    toggleSection('berkas-awal');
    
    // Auto-expand quick actions jika ada revisi pending
    @if($record->revisions()->where('status', 'pending')->count() > 0)
        toggleSection('quick-actions');
    @endif
    
    // Auto-expand all revisions jika ada revisi
    @if($record->revisions()->count() > 0)
        toggleSection('all-revisions');
    @endif
});
</script>