<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Loop untuk setiap KATEGORI layanan --}}
        @forelse($layanans as $layanan)
            {{-- Hanya tampilkan kategori jika memiliki sub-layanan yang aktif --}}
            @if($layanan->subLayanans->isNotEmpty())
                <section class="p-6 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $layanan->nama_layanan }}
                    </h2>

                    {{-- Garis pemisah --}}
                    <hr class="my-4 border-gray-200 dark:border-gray-700">

                    {{-- Loop untuk setiap SUB-LAYANAN di dalam kategori --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($layanan->subLayanans as $sub)
                            <div class="flex flex-col items-start p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $sub->name }}
                                </h3>
                                <div class="flex-grow">
                                    {{-- Bisa ditambahkan deskripsi singkat sub layanan jika ada --}}
                                </div>
                                <div class="w-full mt-4">
                                    <x-filament::button
                                        tag="a"
                                        href="{{ \App\Filament\Warga\Resources\PermohonanResource::getUrl('create', ['sub_layanan_id' => $sub->id]) }}"
                                        icon="heroicon-m-pencil-square"
                                        class="w-full"
                                    >
                                        Pilih & Ajukan
                                    </x-filament::button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
            <div class="flex items-center justify-center w-full p-12 text-center bg-white rounded-xl dark:bg-gray-800">
                <div class="flex flex-col items-center">
                    <x-heroicon-o-document-magnifying-glass class="w-16 h-16 text-gray-400"/>
                    <p class="mt-4 font-semibold text-gray-500">
                        Saat ini belum ada layanan yang dapat diajukan.
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>