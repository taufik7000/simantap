@php
    $anggota = $getRecord()->anggotaKeluarga;
@endphp

<div class="space-y-4">
    @forelse ($anggota as $item)
        <div class="grid grid-cols-3 gap-4 p-4 rounded-lg border bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Anggota</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $item->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIK</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $item->nik }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-1">
                    <x-filament::badge>
                        {{ $item->status_keluarga }}
                    </x-filament::badge>
                </dd>
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
            Belum ada anggota keluarga
        </div>
    @endforelse
</div>