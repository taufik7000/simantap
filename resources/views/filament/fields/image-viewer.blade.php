<div class="p-2">
    @if ($getState())
        <a href="{{ Storage::url($getState()) }}" target="_blank">
            <img src="{{ Storage::url($getState()) }}" alt="Dokumen" class="w-full h-auto rounded-lg shadow-md hover:shadow-xl transition-shadow">
        </a>
    @else
        <div class="p-4 text-center text-gray-400 border-2 border-dashed rounded-lg">
            Tidak ada file
        </div>
    @endif
</div>