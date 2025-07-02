<x-filament-panels::page>
    <div
        x-data="{
            // selectedService sekarang akan menjadi string nama layanan yang dipilih
            selectedService: null, // Biarkan sebagai string nama layanan
            
            requirements: '',
            // UBAH INI: properti untuk ID dan Nama formulir harus array
            selectedFormulirIds: [],    // Ini akan menampung array ID
            selectedFormulirNamas: [],  // Ini akan menampung array nama
            
            selectService(service) {
                this.selectedService = service.nama;
                this.requirements = service.deskripsi;
                // UBAH INI: Pastikan service.formulir_master_id dan service.nama_formulir
                //           diperlakukan sebagai array jika multiple selection
                this.selectedFormulirIds = service.formulir_master_id || []; // Pastikan ini array, jika null/undefined jadi array kosong
                this.selectedFormulirNamas = service.nama_formulir || []; // Pastikan ini array, jika null/undefined jadi array kosong
                
                // Pastikan Livewire state diperbarui secara eksplisit untuk visibilitas unggah dokumen
                $wire.set('data.data_pemohon.jenis_permohonan', service.nama); 
            },

            // Tambahkan init() untuk menangani kasus edit atau halaman reload dengan data
            init() {
                // Ambil nilai awal dari Livewire state jika ada
                const initialLivewireJenisPermohonan = @js($this->data['data_pemohon']['jenis_permohonan'] ?? null);
                if (initialLivewireJenisPermohonan) {
                    // Cari data layanan yang cocok
                    const initialService = {{ Js::from($this->jenisPermohonanData) }}.find(service => service.nama === initialLivewireJenisPermohonan);
                    if (initialService) {
                        this.selectedService = initialService.nama; // Set selectedService lokal
                        this.requirements = initialService.deskripsi;
                        this.selectedFormulirIds = initialService.formulir_master_id || [];
                        this.selectedFormulirNamas = initialService.nama_formulir || [];
                    }
                }
            }
        }"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    >
        <form wire:submit="create">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <div class="lg:col-span-3 space-y-6">
                    <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pilih Jenis Permohonan</h2>
                        </x-slot>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                            @foreach($this->jenisPermohonanData as $service)
                                <div
                                    @click="selectService(@js($service))"
                                    :class="{
                                        // Kelas untuk saat item TERPILIH (hijau)
                                        'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600': selectedService === '{{ $service['nama'] }}',
                                        
                                        // Kelas untuk saat item TIDAK TERPILIH (default, dengan border yang tetap jelas)
                                        'border-gray-300 dark:border-gray-600 hover:border-primary-500 bg-white dark:bg-gray-800': selectedService !== '{{ $service['nama'] }}'
                                    }"
                                    class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md border"
                                >
                                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $service['nama'] }}</h3>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>

                    {{-- UNDUH FORMULIR WAJIB - MENDUKUNG MULTIPLE FILES --}}
                    {{-- Kondisi x-show: section muncul jika ada setidaknya satu ID formulir di array --}}
                    <div x-show="selectedFormulirIds.length > 0" x-transition>
                        <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                             <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Unduh Formulir Wajib</h2>
                            </x-slot>
                            <div class="p-6">
                                <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-4">
                                    Layanan ini memerlukan formulir fisik. Silakan unduh, isi, lalu unggah bersama dokumen lainnya.
                                </p>
                                {{-- Gunakan x-for untuk mengiterasi setiap formulir dan membuat tombol unduh terpisah --}}
                                <div class="space-y-3"> {{-- Memberi jarak antar tombol unduh --}}
                                    <template x-for="(formId, index) in selectedFormulirIds" :key="index">
                                        <a x-bind:href="`/download-formulir-master/${formId}`"
                                           class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:hover:bg-white/10 transition-all duration-200"
                                        >
                                            <x-heroicon-m-arrow-down-tray class="w-5 h-5"/>
                                            {{-- Menampilkan nama formulir yang sesuai dengan indeks --}}
                                            <span x-text="selectedFormulirNamas[index] ? `Unduh: ${selectedFormulirNamas[index]}` : 'Unduh Formulir'"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </x-filament::section>
                    </div>

                    <div x-show="selectedService" x-transition.opacity>
                        <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Deskripsi & Persyaratan</h2>
                            </x-slot>
                            <div class="prose max-w-none dark:prose-invert p-6" x-html="requirements"></div>
                        </x-filament::section>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6"> 
                    {{-- UNGGAH DOKUMEN --}}
                    <div x-show="selectedService" x-transition.delay.200ms>
                        <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden !p-0">
                            <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                                    <span x-text="'Unggah Dokumen untuk: ' + selectedService"></span>
                                </h2>
                            </x-slot>
                            <div class="p-6 space-y-6">
                                {{ $this->form }}
                            </div>
                        </x-filament::section>
                    </div>

                    {{-- TOMBOL AJUKAN PERMOHONAN SEKARANG --}}
                    <div x-show="selectedService" x-transition.delay.300ms> 
                        <div class="flex justify-end"> 
                            <x-filament::button type="submit" wire:loading.attr="disabled">
                                Ajukan Permohonan Sekarang
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>