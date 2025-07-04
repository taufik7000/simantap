@php
    $record = $getRecord();
    $latestRevision = $record->revisions()->where('status', 'pending')->latest()->first();
@endphp

<div class="space-y-3">
    {{-- Quick Status Updates --}}
    <div class="space-y-2">
        <h4 class="text-sm font-medium text-gray-700">Update Status Cepat:</h4>
        
        @if($record->status === 'baru' || $record->status === 'sedang_ditinjau')
            <form action="{{ route('petugas.quick-status-update') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="permohonan_id" value="{{ $record->id }}">
                <input type="hidden" name="status" value="verifikasi_berkas">
                <button type="submit" 
                        onclick="return confirm('Mulai verifikasi berkas untuk permohonan ini?')"
                        class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                    <x-heroicon-o-document-check class="w-4 h-4 inline mr-2" />
                    Mulai Verifikasi
                </button>
            </form>
        @endif

        @if(in_array($record->status, ['verifikasi_berkas', 'sedang_ditinjau']))
            <form action="{{ route('petugas.quick-status-update') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="permohonan_id" value="{{ $record->id }}">
                <input type="hidden" name="status" value="diproses">
                <button type="submit"
                        onclick="return confirm('Mulai memproses permohonan ini?')"
                        class="w-full text-left px-3 py-2 text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md hover:bg-yellow-100 transition-colors">
                    <x-heroicon-o-cog class="w-4 h-4 inline mr-2" />
                    Mulai Proses
                </button>
            </form>
        @endif

        @if($record->status === 'diproses')
            <form action="{{ route('petugas.quick-status-update') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="permohonan_id" value="{{ $record->id }}">
                <input type="hidden" name="status" value="disetujui">
                <button type="submit"
                        onclick="return confirm('Setujui permohonan ini?')"
                        class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                    <x-heroicon-o-check-circle class="w-4 h-4 inline mr-2" />
                    Setujui
                </button>
            </form>
        @endif
    </div>

    {{-- Revision Actions --}}
    @if($latestRevision)
        <div class="space-y-2 pt-3 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700">Aksi Revisi:</h4>
            
            <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="revision_id" value="{{ $latestRevision->id }}">
                <input type="hidden" name="action" value="approve">
                <button type="submit"
                        onclick="return confirm('Terima revisi ke-{{ $latestRevision->revision_number }}?')"
                        class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                    <x-heroicon-o-check class="w-4 h-4 inline mr-2" />
                    Terima Revisi
                </button>
            </form>
            
            <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="revision_id" value="{{ $latestRevision->id }}">
                <input type="hidden" name="action" value="reject">
                <button type="submit"
                        onclick="return confirm('Tolak revisi ke-{{ $latestRevision->revision_number }}?')"
                        class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                    <x-heroicon-o-x-mark class="w-4 h-4 inline mr-2" />
                    Tolak Revisi
                </button>
            </form>
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
            <a href="{{ route('berkas.download-all', $record) }}"
               class="w-full text-left px-3 py-2 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-md hover:bg-indigo-100 transition-colors block">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 inline mr-2" />
                Unduh Semua Berkas
            </a>
        </div>
    @endif
</div>