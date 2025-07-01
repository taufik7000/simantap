<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Loop untuk Kategori Induk --}}
        @forelse($layanans as $layanan)
            @if($layanan->subLayanans->isNotEmpty())
                <section class="p-6 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    
                    {{-- Header Kategori Induk --}}
                    <div class="flex items-center gap-x-4">
                        <div class="flex-shrink-0 p-3 bg-gray-100 rounded-full dark:bg-gray-700">
                            <x-dynamic-component 
                                :component="$layanan->icon ?? 'heroicon-o-document-text'" 
                                class="w-6 h-6 text-gray-600 dark:text-gray-300" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                                {{ $layanan->name }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Pilih salah satu jenis layanan yang ingin Anda ajukan di bawah ini.
                            </p>
                        </div>
                    </div>

                    <hr class="my-5 border-gray-200 dark:border-gray-700">

                    {{-- Grid untuk Daftar Sub-Layanan --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($layanan->subLayanans as $sub)
                            <div class="flex flex-col p-4 transition-all duration-200 border border-gray-200 rounded-lg hover:shadow-md hover:border-primary-500 dark:border-gray-700 dark:hover:border-primary-400">
                                
                                {{-- ▼▼▼ BAGIAN INI YANG DIPERBARUI ▼▼▼ --}}
                                <div class="flex items-center flex-grow mb-4">
                                    {{-- Tampilkan Ikon Sub-Layanan --}}
                                    <x-dynamic-component 
                                        :component="$sub->icon ?? 'heroicon-o-document'" 
                                        class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                                    
                                    {{-- Tampilkan Nama Sub-Layanan --}}
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $sub->name }}
                                    </h3>
                                </div>
                                {{-- ▲▲▲ SAMPAI DI SINI ▲▲▲ --}}

                                <div class="w-full">
                                    <x-filament::button
                                        tag="a"
                                        href="{{ \App\Filament\Warga\Resources\PermohonanResource::getUrl('create', ['sub_layanan_id' => $sub->id]) }}"
                                        icon="heroicon-m-pencil-square"
                                        class="w-full"
                                    >
                                        Ajukan
                                    </x-filament::button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
            {{-- ... Tampilan jika tidak ada layanan ... --}}
        @endforelse
    </div>
</x-filament-panels::page>