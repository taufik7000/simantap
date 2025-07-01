<x-filament-panels::page>
    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Header Section --}}

        {{-- Loop untuk Kategori Induk --}}
        {{-- Menggunakan $kategoriLayanans sesuai dengan variabel di PilihLayanan.php --}}
        @forelse($kategoriLayanans as $kategoriLayanan) {{-- UBAH: $layanans menjadi $kategoriLayanans --}}
            {{-- Memeriksa apakah kategori layanan ini memiliki layanan aktif di dalamnya --}}
            @if($kategoriLayanan->layanans->isNotEmpty()) {{-- UBAH: $kategori_layanan->subLayanans menjadi $kategoriLayanan->layanans --}}
                <section class="overflow-hidden bg-white rounded-2xl shadow-lg ring-1 ring-gray-200/50 dark:bg-gray-800 dark:ring-white/10 transition-all duration-300 hover:shadow-xl">
                    {{-- Header Kategori Induk --}}
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-600 px-8 py-6">
                        <div class="flex items-center gap-x-6">
                            <div class="flex-shrink-0 p-4 bg-white rounded-xl shadow-sm dark:bg-gray-800">
                                {{-- Menggunakan ikon dari KategoriLayanan --}}
                                <x-dynamic-component
                                    :component="$kategoriLayanan->icon ?? 'heroicon-o-document-text'" {{-- UBAH: $layanan->icon menjadi $kategoriLayanan->icon --}}
                                    class="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                {{-- Menampilkan nama dari KategoriLayanan --}}
                                <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">
                                    {{ $kategoriLayanan->name }} {{-- UBAH: $layanan->name menjadi $kategoriLayanan->name --}}
                                </h2>
                                <p class="text-base text-gray-600 dark:text-gray-300">
                                    Pilih salah satu jenis layanan yang ingin Anda ajukan di bawah ini
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Grid untuk Daftar Layanan (yang dulunya Sub-Layanan) --}}
                    <div class="p-8">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            {{-- Mengiterasi relasi 'layanans' dari KategoriLayanan --}}
                            @foreach($kategoriLayanan->layanans as $layananItem) {{-- UBAH: $layanan->subLayanans as $sub menjadi $kategoriLayanan->layanans as $layananItem --}}
                                <div class="group relative overflow-hidden bg-gradient-to-br from-white to-gray-50 dark:from-gray-700 dark:to-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 transition-all duration-300 hover:shadow-lg hover:scale-105 hover:border-emerald-300 dark:hover:border-emerald-500">
                                    {{-- Background Pattern --}}
                                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    
                                    {{-- Content --}}
                                    <div class="relative p-6 flex flex-col h-full">
                                        {{-- Icon dan Title Section --}}
                                        <div class="flex items-start gap-4 mb-6 flex-grow">
                                            <div class="flex-shrink-0 p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition-colors duration-300">
                                                {{-- Menggunakan ikon dari objek Layanan ($layananItem) --}}
                                                <x-dynamic-component
                                                    :component="$layananItem->icon ?? 'heroicon-o-document'" {{-- UBAH: $sub->icon menjadi $layananItem->icon --}}
                                                    class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                {{-- Menampilkan nama dari objek Layanan ($layananItem) --}}
                                                <h3 class="font-semibold text-gray-900 dark:text-white text-base leading-6 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors duration-300">
                                                    {{ $layananItem->name }} {{-- UBAH: $sub->name menjadi $layananItem->name --}}
                                                </h3>
                                            </div>
                                        </div>

                                        {{-- Action Button --}}
                                        <div class="mt-auto">
                                            <x-filament::button
                                                tag="a"
                                                {{-- Menggunakan parameter 'layanan_id' dan ID dari objek Layanan ($layananItem) --}}
                                                href="{{ \App\Filament\Warga\Resources\PermohonanResource::getUrl('create', ['layanan_id' => $layananItem->id]) }}" {{-- UBAH: 'sub_layanan_id' menjadi 'layanan_id' dan $sub->id menjadi $layananItem->id --}}
                                                icon="heroicon-m-pencil-square"
                                                class="w-full justify-center bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 border-0 shadow-sm hover:shadow-md transition-all duration-300 group-hover:scale-105"
                                                size="sm"
                                            >
                                                <span class="font-medium">Ajukan Sekarang</span>
                                            </x-filament::button>
                                        </div>
                                    </div>

                                    {{-- Hover Effect Border --}}
                                    <div class="absolute inset-0 rounded-xl ring-2 ring-emerald-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @empty
            {{-- Empty State --}}
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Belum Ada Layanan Tersedia
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Saat ini belum ada layanan administrasi yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.
                </p>
            </div>
        @endforelse

        {{-- Quick Access Section --}}
        {{-- Menggunakan $kategoriLayanans untuk memeriksa apakah ada data --}}
        @if($kategoriLayanans->isNotEmpty()) {{-- UBAH: $layanans->isNotEmpty() menjadi $kategoriLayanans->isNotEmpty() --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white">
                <div class="text-center">
                    <h3 class="text-xl font-bold mb-3">Butuh Bantuan?</h3>
                    <p class="text-emerald-100 mb-6 max-w-2xl mx-auto">
                        Tim layanan kami siap membantu Anda dalam proses pengajuan dokumen administrasi kependudukan
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#" class="inline-flex items-center px-6 py-3 bg-white text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-colors duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Hubungi Kami
                        </a>
                        <a href="#" class="inline-flex items-center px-6 py-3 bg-white/20 text-white font-semibold rounded-lg hover:bg-white/30 transition-colors duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            FAQ
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Custom Styles --}}
    <style>
        /* Custom utility classes for emerald color consistency */
    </style>
</x-filament-panels::page>