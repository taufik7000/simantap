{{-- resources/views/filament/infolists/components/download-link.blade.php --}}
@php
    // Set default values untuk variable yang mungkin tidak ada
    $color = $color ?? 'primary';
    $icon = $icon ?? 'heroicon-o-document';
    $label = $label ?? 'Unduh';
    $filename = $filename ?? 'File';
    
    // Get file size safely
    $fileSize = 'Ukuran tidak diketahui';
    if (isset($filePath) && \Storage::disk('private')->exists($filePath)) {
        $sizeInBytes = \Storage::disk('private')->size($filePath);
        $fileSize = \Illuminate\Support\Number::fileSize($sizeInBytes);
    }
@endphp

<div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
    <div class="flex items-center space-x-3">
        <x-dynamic-component :component="$icon" class="w-5 h-5 {{ $color === 'warning' ? 'text-yellow-500' : ($color === 'danger' ? 'text-red-500' : 'text-gray-500') }}" />
        <div>
            <p class="text-sm font-medium text-gray-900">{{ $filename }}</p>
            <p class="text-xs text-gray-500">{{ $fileSize }}</p>
        </div>
    </div>
    <a href="{{ $url }}" 
       target="_blank"
       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md {{ $color === 'warning' ? 'text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:ring-yellow-500' : ($color === 'danger' ? 'text-red-700 bg-red-100 hover:bg-red-200 focus:ring-red-500' : 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:ring-indigo-500') }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
        {{ $label }}
    </a>
</div>