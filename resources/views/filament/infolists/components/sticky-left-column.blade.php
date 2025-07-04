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

    <!-- QUICK SUMMARY REVISI -->
    @if($record->revisions()->count() > 0)
        @php
            $revisionsCount = $record->revisions()->count();
            $pendingCount = $record->revisions()->where('status', 'pending')->count();
            $approvedCount = $record->revisions()->where('status', 'approved')->count();
            $rejectedCount = $record->revisions()->where('status', 'rejected')->count();
        @endphp
        
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-500 mr-2" />
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Revisi</h3>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $revisionsCount }} total
                </span>
            </div>
            
            <div class="grid grid-cols-3 gap-4 text-sm">
                @if($pendingCount > 0)
                    <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="text-lg font-semibold text-yellow-800">{{ $pendingCount }}</div>
                        <div class="text-yellow-600">Menunggu</div>
                    </div>
                @endif
                
                @if($approvedCount > 0)
                    <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-lg font-semibold text-green-800">{{ $approvedCount }}</div>
                        <div class="text-green-600">Diterima</div>
                    </div>
                @endif
                
                @if($rejectedCount > 0)
                    <div class="text-center p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="text-lg font-semibold text-red-800">{{ $rejectedCount }}</div>
                        <div class="text-red-600">Ditolak</div>
                    </div>
                @endif
            </div>
            
            <!-- Lihat detail link -->
            <div class="mt-4 text-center">
                <button onclick="toggleRevisionDetail()" 
                        class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                    Lihat Detail Semua Revisi →
                </button>
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

/* Smooth scroll behavior */
.sticky {
    scroll-behavior: smooth;
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