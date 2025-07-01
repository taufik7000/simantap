<x-filament-panels::page>
    @if($subLayanans->isNotEmpty())
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($subLayanans as $sub)
                <div class="p-6 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $sub->name }}
                    </h3>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        @if(is_array($sub->description) && !empty($sub->description[0]['nama_syarat']))
                            <p>Syarat utama: {{ $sub->description[0]['nama_syarat'] }}</p>
                        @endif
                    </div>
                    <div class="mt-4">

                        <x-filament::button
                            tag="a"
                            href="{{ \App\Filament\Warga\Resources\PermohonanResource::getUrl('create', ['sub_layanan_id' => $sub->id]) }}"
                            icon="heroicon-m-pencil-square"
                        >
                            Ajukan Sekarang
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- ... --}}
    @endif
</x-filament-panels::page>