<div class="space-y-3">

    {{-- Communication Actions --}}
    <div class="space-y-2 pt-3 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-700">Komunikasi:</h4>

        {{-- Menggunakan $getRecord() untuk mengakses data --}}
        <a href="mailto:{{ $getRecord()->user->email }}?subject=Permohonan {{ $getRecord()->kode_permohonan }}"
           class="w-full text-left px-3 py-2 text-xs bg-gray-50 text-gray-700 border border-gray-200 rounded-md hover:bg-gray-100 transition-colors block">
            <x-heroicon-o-envelope class="w-4 h-4 inline mr-2" />
            Email Warga
        </a>

        {{-- Menggunakan $getRecord() di sini juga --}}
        @if($getRecord()->user->nomor_whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $getRecord()->user->nomor_whatsapp) }}?text=Halo, terkait permohonan {{ $getRecord()->kode_permohonan }}"
               target="_blank"
               class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors block">
                <x-heroicon-o-chat-bubble-oval-left class="w-4 h-4 inline mr-2" />
                WhatsApp
            </a>
        @endif
    </div>

    {{-- Download All Files --}}
    {{-- Menggunakan $getRecord() di sini juga --}}
    @if($getRecord()->berkas_pemohon || $getRecord()->revisions()->whereNotNull('berkas_revisi')->count() > 0)
        <div class="pt-3 border-t border-gray-200">
            <a href="{{ route('berkas.download-all', $getRecord()) }}"
               class="w-full text-left px-3 py-2 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-md hover:bg-indigo-100 transition-colors block">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4 inline mr-2" />
                Unduh Semua Berkas
            </a>
        </div>
    @endif
</div>