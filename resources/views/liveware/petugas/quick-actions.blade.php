@php
    $latestRevision = $permohonan->revisions()->where('status', 'pending')->latest()->first();
@endphp

<div class="space-y-3">
    {{-- Quick Status Updates --}}
    <div class="space-y-2">
        <h4 class="text-sm font-medium text-gray-700">Update Status Cepat:</h4>
        
        @if($permohonan->status === 'baru' || $permohonan->status === 'sedang_ditinjau')
            <button type="button" 
                    wire:click="quickStatusUpdate({{ $permohonan->id }}, 'verifikasi_berkas')"
                    class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                <x-heroicon-o-document-check class="w-4 h-4 inline mr-2" />
                Mulai Verifikasi
            </button>
        @endif

        @if(in_array($permohonan->status, ['verifikasi_berkas', 'sedang_ditinjau']))
            <button type="button" 
                    wire:click="quickStatusUpdate({{ $permohonan->id }}, 'diproses')"
                    class="w-full text-left px-3 py-2 text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md hover:bg-yellow-100 transition-colors">
                <x-heroicon-o-cog class="w-4 h-4 inline mr-2" />
                Mulai Proses
            </button>
        @endif

        @if($permohonan->status === 'diproses')
            <button type="button" 
                    wire:click="quickStatusUpdate({{ $permohonan->id }}, 'disetujui')"
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
                    wire:click="approveRevision({{ $latestRevision->id }})"
                    wire:confirm="Apakah Anda yakin ingin menerima revisi ini?"
                    class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                <x-heroicon-o-check class="w-4 h-4 inline mr-2" />
                Terima Revisi
            </button>
            
            <button type="button" 
                    wire:click="rejectRevision({{ $latestRevision->id }})"
                    wire:confirm="Apakah Anda yakin ingin menolak revisi ini?"
                    class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                <x-heroicon-o-x-mark class="w-4 h-4 inline mr-2" />
                Tolak Revisi
            </button>
        </div>
    @endif

    {{-- Communication Actions --}}
    <div class="space-y-2 pt-3 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-700">Komunikasi:</h4>
        
        <a href="mailto:{{ $permohonan->user->email }}?subject=Permohonan {{ $permohonan->kode_permohonan }}"
           class="w-full text-left px-3 py-2 text-xs bg-gray-50 text-gray-700 border border-gray-200 rounded-md hover:bg-gray-100 transition-colors block">
            <x-heroicon-o-envelope class="w-4 h-4 inline mr-2" />
            Email Warga
        </a>
        
        @if($permohonan->user->nomor_telepon)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $permohonan->user->nomor_telepon) }}?text=Halo, terkait permohonan {{ $permohonan->kode_permohonan }}"
               target="_blank"
               class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors block">
                <x-heroicon-o-chat-bubble-oval-left class="w-4 h-4 inline mr-2" />
                WhatsApp
            </a>
        @endif
    </div>

    {{-- Download All Files --}}
    @if($permohonan->berkas_pemohon || $permohonan->revisions()->whereNotNull('berkas_revisi')->count() > 0)
        <div class="pt-3 border-t border-gray-200">
            <button type="button" 
                    wire:click="downloadAllFiles({{ $permohonan->id }})"
                    class="w-full text-left px-3 py-2 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-md hover:bg-indigo-100 transition-colors">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 inline mr-2" />
                Unduh Semua Berkas
            </button>
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading class="flex items-center justify-center py-2">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-xs text-gray-600">Memproses...</span>
    </div>
</div>