@php
    $record = $getRecord();
    $latestRevision = $record->revisions()->where('status', 'pending')->latest()->first();
@endphp

<div class="space-y-3">
    {{-- Quick Status Updates --}}
    <div class="space-y-2">
        <h4 class="text-sm font-medium text-gray-700">Update Status Cepat:</h4>
        
        @if($record->status === 'baru' || $record->status === 'sedang_ditinjau')
            <button type="button" 
                    onclick="Livewire.dispatch('quickStatusUpdate', { id: {{ $record->id }}, status: 'verifikasi_berkas' })"
                    class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                <x-heroicon-o-document-check class="w-4 h-4 inline mr-2" />
                Mulai Verifikasi
            </button>
        @endif

        @if(in_array($record->status, ['verifikasi_berkas', 'sedang_ditinjau']))
            <button type="button" 
                    onclick="Livewire.dispatch('quickStatusUpdate', { id: {{ $record->id }}, status: 'diproses' })"
                    class="w-full text-left px-3 py-2 text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md hover:bg-yellow-100 transition-colors">
                <x-heroicon-o-cog class="w-4 h-4 inline mr-2" />
                Mulai Proses
            </button>
        @endif

        @if($record->status === 'diproses')
            <button type="button" 
                    onclick="Livewire.dispatch('quickStatusUpdate', { id: {{ $record->id }}, status: 'disetujui' })"
                    class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                <x-heroicon-o-check-circle class="w-4 h-4 inline mr-2" />
                Setujui
            </button>
        @endif
    </div>

    {{-- Revision Actions --}}
    @if($latestRevision)
        <div class="space-y-2 pt-3 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700">Aksi Revisi:</h4>
            
            <button type="button" 
                    onclick="Livewire.dispatch('approveRevision', { id: {{ $latestRevision->id }} })"
                    class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                <x-heroicon-o-check class="w-4 h-4 inline mr-2" />
                Terima Revisi
            </button>
            
            <button type="button" 
                    onclick="Livewire.dispatch('rejectRevision', { id: {{ $latestRevision->id }} })"
                    class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                <x-heroicon-o-x-mark class="w-4 h-4 inline mr-2" />
                Tolak Revisi
            </button>
        </div>
    @endif

    {{-- Communication Actions --}}
    <div class="space-y-2 pt-3 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-700">Komunikasi:</h4>
        
        <a href="mailto:{{ $record->user->email }}?subject=Permohonan {{ $record->kode_permohonan }}"
           class="w-full text-left px-3 py-2 text-xs bg-gray-50 text-gray-700 border border-gray-200 rounded-md hover:bg-gray-100 transition-colors block">
            <x-heroicon-o-envelope class="w-4 h-4 inline mr-2" />
            Email Warga
        </a>
        
        @if($record->user->nomor_telepon)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $record->user->nomor_telepon) }}?text=Halo, terkait permohonan {{ $record->kode_permohonan }}"
               target="_blank"
               class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors block">
                <x-heroicon-o-chat-bubble-oval-left class="w-4 h-4 inline mr-2" />
                WhatsApp
            </a>
        @endif
    </div>

    {{-- Download All Files --}}
    @if($record->berkas_pemohon || $record->revisions()->whereNotNull('berkas_revisi')->count() > 0)
        <div class="pt-3 border-t border-gray-200">
            <button type="button" 
                    onclick="Livewire.dispatch('downloadAllFiles', { id: {{ $record->id }} })"
                    class="w-full text-left px-3 py-2 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-md hover:bg-indigo-100 transition-colors">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 inline mr-2" />
                Unduh Semua Berkas
            </button>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick status update handler
    Livewire.on('quickStatusUpdate', (data) => {
        // This would integrate with your Livewire component
        console.log('Quick status update:', data);
    });
    
    // Revision approval handler
    Livewire.on('approveRevision', (data) => {
        console.log('Approve revision:', data);
    });
    
    // Revision rejection handler  
    Livewire.on('rejectRevision', (data) => {
        console.log('Reject revision:', data);
    });
    
    // Download all files handler
    Livewire.on('downloadAllFiles', (data) => {
        console.log('Download all files:', data);
    });
});
</script>