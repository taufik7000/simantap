{{-- resources/views/filament/infolists/components/sticky-left-column.blade.php --}}
@php
    $record = $getRecord();
@endphp

<div class="sticky top-4 max-h-screen overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 pr-2">
    <!-- INFORMASI PERMOHONAN -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
        <div class="flex items-center mb-4">
            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 mr-2" />
            <h3 class="text-lg font-semibold text-gray-900">Informasi Permohonan</h3>
        </div>
        
        <!-- Header Info Grid -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kode Permohonan</label>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900">{{ $record->kode_permohonan }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $record->kode_permohonan }}')" 
                            class="text-xs text-indigo-600 hover:text-indigo-500">
                        <x-heroicon-o-clipboard class="w-4 h-4" />
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Jenis Permohonan</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $record->data_pemohon['jenis_permohonan'] ?? 'Tidak diketahui' }}
                </span>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</label>
                @php
                    $statusConfig = [
                        'baru' => ['label' => 'Baru Diajukan', 'color' => 'bg-gray-100 text-gray-800'],
                        'sedang_ditinjau' => ['label' => 'Sedang Ditinjau', 'color' => 'bg-yellow-100 text-yellow-800'],
                        'verifikasi_berkas' => ['label' => 'Verifikasi Berkas', 'color' => 'bg-blue-100 text-blue-800'],
                        'diproses' => ['label' => 'Sedang Diproses', 'color' => 'bg-indigo-100 text-indigo-800'],
                        'membutuhkan_revisi' => ['label' => 'Membutuhkan Revisi', 'color' => 'bg-red-100 text-red-800'],
                        'butuh_perbaikan' => ['label' => 'Butuh Perbaikan', 'color' => 'bg-orange-100 text-orange-800'],
                        'disetujui' => ['label' => 'Disetujui', 'color' => 'bg-green-100 text-green-800'],
                        'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-red-100 text-red-800'],
                        'selesai' => ['label' => 'Selesai', 'color' => 'bg-green-100 text-green-800'],
                    ];
                    $currentStatus = $statusConfig[$record->status] ?? ['label' => $record->status, 'color' => 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentStatus['color'] }}">
                    {{ $currentStatus['label'] }}
                </span>
            </div>
        </div>
        
        <!-- Detail Info Grid -->
        <div class="grid grid-cols-4 gap-4 text-sm">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Diajukan</label>
                <div class="flex items-center">
                    <x-heroicon-s-calendar class="w-4 h-4 text-gray-400 mr-1" />
                    <span>{{ $record->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Terakhir Update</label>
                <div class="flex items-center">
                    <x-heroicon-s-clock class="w-4 h-4 text-gray-400 mr-1" />
                    <span>{{ $record->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Ditugaskan ke</label>
                @if($record->assignedTo)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-800">
                        {{ $record->assignedTo->name }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">
                        Belum Ditugaskan
                    </span>
                @endif
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Prioritas</label>
                @php
                    $hours = now()->diffInHours($record->created_at);
                    $priority = $hours > 72 ? ['label' => 'Tinggi', 'color' => 'bg-red-100 text-red-800'] :
                               ($hours > 24 ? ['label' => 'Sedang', 'color' => 'bg-yellow-100 text-yellow-800'] :
                                             ['label' => 'Normal', 'color' => 'bg-green-100 text-green-800']);
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $priority['color'] }}">
                    {{ $priority['label'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- DATA PEMOHON -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
        <div class="flex items-center mb-4">
            <x-heroicon-o-user class="w-5 h-5 text-gray-500 mr-2" />
            <h3 class="text-lg font-semibold text-gray-900">Data Pemohon</h3>
        </div>
        
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nama Lengkap</label>
                <div class="flex items-center">
                    <x-heroicon-s-user class="w-4 h-4 text-gray-400 mr-1" />
                    <span class="font-medium">{{ $record->user->name }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">NIK</label>
                <div class="flex items-center">
                    <x-heroicon-s-identification class="w-4 h-4 text-gray-400 mr-1" />
                    <span>{{ $record->user->nik }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">No. KK</label>
                <div class="flex items-center">
                    <x-heroicon-s-home class="w-4 h-4 text-gray-400 mr-1" />
                    <span>{{ $record->user->nomor_kk }}</span>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                <div class="flex items-center">
                    <x-heroicon-s-envelope class="w-4 h-4 text-gray-400 mr-1" />
                    <a href="mailto:{{ $record->user->email }}" class="text-indigo-600 hover:text-indigo-500">
                        {{ $record->user->email }}
                    </a>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">No. Telepon</label>
                <div class="flex items-center">
                    <x-heroicon-s-phone class="w-4 h-4 text-gray-400 mr-1" />
                    @if($record->user->nomor_telepon)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $record->user->nomor_telepon) }}" 
                           target="_blank" 
                           class="text-green-600 hover:text-green-500">
                            {{ $record->user->nomor_telepon }}
                        </a>
                    @else
                        <span class="text-gray-400">Tidak ada</span>
                    @endif
                </div>
            </div>
            
            <div class="col-span-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Alamat</label>
                <div class="flex items-start">
                    <x-heroicon-s-map-pin class="w-4 h-4 text-gray-400 mr-1 mt-0.5" />
                    <span>{{ $record->user->alamat ?? 'Alamat tidak tersedia' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CATATAN PETUGAS -->
    @if(!empty($record->catatan_petugas))
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center mb-4">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-gray-500 mr-2" />
                <h3 class="text-lg font-semibold text-gray-900">Catatan & Komunikasi</h3>
            </div>
            
            @php
                $noteColor = match($record->status) {
                    'membutuhkan_revisi', 'butuh_perbaikan', 'ditolak' => 'border-red-200 bg-red-50',
                    'disetujui', 'selesai' => 'border-green-200 bg-green-50',
                    default => 'border-blue-200 bg-blue-50'
                };
            @endphp
            
            <div class="border rounded-lg p-4 {{ $noteColor }}">
                <div class="prose prose-sm max-w-none">
                    {!! Str::markdown($record->catatan_petugas) !!}
                </div>
            </div>
        </div>
    @endif

    <!-- SEMUA REVISI -->
    @if($record->revisions()->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-900">Semua Revisi</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $record->revisions()->count() }} revisi
                </span>
            </div>
            
            <!-- List All Revisions -->
            <div class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                @foreach($record->revisions()->orderBy('revision_number', 'desc')->get() as $revision)
                    <div class="border rounded-lg p-3 {{ $revision->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : ($revision->status === 'accepted' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $revision->revision_number }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">Revisi ke-{{ $revision->revision_number }}</span>
                            </div>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                @if($revision->status === 'pending')
                                    Menunggu
                                @elseif($revision->status === 'accepted')
                                    Diterima
                                @else
                                    Ditolak
                                @endif
                            </span>
                        </div>
                        
                        <div class="text-xs text-gray-600 mb-2">
                            {{ $revision->created_at->format('d M Y H:i') }} - {{ $revision->created_at->diffForHumans() }}
                        </div>
                        
                        @if($revision->catatan_revisi)
                            <p class="text-xs text-gray-700 mb-2 line-clamp-2">{{ $revision->catatan_revisi }}</p>
                        @endif
                        
                        @if($revision->berkas_revisi && count($revision->berkas_revisi) > 0)
                            <div class="flex items-center text-xs text-blue-600 mb-2">
                                <x-heroicon-o-document class="w-3 h-3 mr-1" />
                                {{ count($revision->berkas_revisi) }} file dilampirkan
                            </div>
                        @endif
                        
                        @if($revision->catatan_petugas)
                            <div class="border-t pt-2 mt-2">
                                <p class="text-xs {{ $revision->status === 'accepted' ? 'text-green-700' : 'text-red-700' }} font-medium">
                                    Catatan: {{ \Illuminate\Support\Str::limit($revision->catatan_petugas, 80) }}
                                </p>
                            </div>
                        @endif
                        
                        <!-- Quick Actions untuk Pending Revisions -->
                        @if($revision->status === 'pending')
                            <div class="flex space-x-2 mt-3 pt-2 border-t">
                                <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit"
                                            onclick="return confirm('Terima revisi ke-{{ $revision->revision_number }}?')"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                        <x-heroicon-o-check class="w-3 h-3 mr-1" />
                                        Terima
                                    </button>
                                </form>
                                
                                <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit"
                                            onclick="return confirm('Tolak revisi ke-{{ $revision->revision_number }}?')"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors">
                                        <x-heroicon-o-x-mark class="w-3 h-3 mr-1" />
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                        <!-- Download Files -->
                        @if($revision->berkas_revisi && count($revision->berkas_revisi) > 0)
                            <div class="mt-2 pt-2 border-t">
                                <button onclick="toggleFiles{{ $revision->id }}()" class="text-xs text-indigo-600 hover:text-indigo-500">
                                    Lihat File ({{ count($revision->berkas_revisi) }})
                                </button>
                                <div id="files{{ $revision->id }}" class="hidden mt-2 space-y-1">
                                    @foreach($revision->berkas_revisi as $index => $berkas)
                                        @if(!empty($berkas['path_dokumen']))
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600 truncate">{{ $berkas['nama_dokumen'] ?? 'File ' . ($index + 1) }}</span>
                                                <a href="{{ route('secure.download.revision', ['revision_id' => $revision->id, 'path' => $berkas['path_dokumen']]) }}"
                                                   target="_blank"
                                                   class="text-indigo-600 hover:text-indigo-500 ml-2">
                                                    <x-heroicon-o-arrow-down-tray class="w-3 h-3" />
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Summary Footer -->
            <div class="mt-4 pt-3 border-t border-gray-200 text-xs text-gray-500">
                <div class="flex justify-between">
                    <span>Total: {{ $record->revisions()->count() }} revisi</span>
                    <span>
                        @php
                            $pendingCount = $record->revisions()->where('status', 'pending')->count();
                            $acceptedCount = $record->revisions()->where('status', 'accepted')->count();
                            $rejectedCount = $record->revisions()->where('status', 'rejected')->count();
                        @endphp
                        
                        @if($pendingCount > 0) {{ $pendingCount }} menunggu @endif
                        @if($acceptedCount > 0) {{ $acceptedCount }} diterima @endif  
                        @if($rejectedCount > 0) {{ $rejectedCount }} ditolak @endif
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Custom scrollbar untuk sticky column */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Ensure sticky positioning works correctly */
@supports (position: sticky) {
    .sticky {
        position: sticky;
    }
}
</style>

<script>
function toggleRevisionDetail() {
    // Scroll ke section revisi di sidebar kanan
    const revisionSection = document.querySelector('[data-section="revisions"]');
    if (revisionSection) {
        revisionSection.scrollIntoView({ behavior: 'smooth' });
        // Buka section jika collapsed
        const collapseButton = revisionSection.querySelector('[data-collapse-toggle]');
        if (collapseButton) {
            collapseButton.click();
        }
    }
}

// Dynamic toggle functions for each revision's files
@foreach($record->revisions()->orderBy('revision_number', 'desc')->get() as $revision)
function toggleFiles{{ $revision->id }}() {
    const filesDiv = document.getElementById('files{{ $revision->id }}');
    if (filesDiv) {
        filesDiv.classList.toggle('hidden');
    }
}
@endforeach

// Auto-expand important sections when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Auto expand quick actions jika ada revisi pending
    @if($record->revisions()->where('status', 'pending')->count() > 0)
        const quickActionsSection = document.querySelector('[data-section="quick-actions"]');
        if (quickActionsSection) {
            const collapseButton = quickActionsSection.querySelector('[data-collapse-toggle]');
            if (collapseButton && quickActionsSection.classList.contains('collapsed')) {
                collapseButton.click();
            }
        }
    @endif
});
</script>