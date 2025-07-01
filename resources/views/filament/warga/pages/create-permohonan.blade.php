<x-filament-panels::page>
    <div
        x-data="{
            selectedService: null,
            requirements: '',
            selectService(service) {
                this.selectedService = service.nama;
                this.requirements = service.deskripsi;
                $wire.set('data.data_pemohon.jenis_permohonan', service.nama); 
            }
        }"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
    >
        <form wire:submit="create">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- Kolom Kiri: Informasi & Pilihan Layanan --}}
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
                                        'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600': selectedService === '{{ $service['nama'] }}', 
                                        'border-gray-300 dark:border-gray-600 hover:border-primary-500': selectedService !== '{{ $service['nama'] }}' 
                                    }"
                                    class="flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md"
                                >
                                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mr-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $service['nama'] }}</h3>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>

                    <div x-show="selectedService" x-transition.opacity>
                        <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Deskripsi & Persyaratan</h2>
                            </x-slot>
                            <div class="prose max-w-none dark:prose-invert p-6" x-html="requirements"></div>
                        </x-filament::section>
                    </div>
                </div>

                {{-- Kolom Kanan: Form Upload --}}
                <div class="lg:col-span-2" x-show="selectedService" x-transition.delay.200ms>
                    <x-filament::section class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden !p-0">
                        <x-slot name="heading" class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                                <span x-text="'Upload Dokumen Syarat Untuk Mengajukan ' + selectedService"></span>
                            </h2>
                        </x-slot>
                        
                        <div class="p-6 space-y-6">
                            {{ $this->form }}
                        </div>
                    </x-filament::section>
                </div>
            </div>
            
            <div 
                x-show="selectedService" 
                x-transition 
                class="mt-8 pt-8 border-t border-gray-200 dark:border-white/10"
            >
                <x-filament::button 
                    type="submit" 
                    wire:loading.attr="disabled" 
                    class="w-full py-4 text-lg font-bold transition-all hover:scale-[1.02]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ajukan Permohonan Sekarang
                </x-filament::button>
            </div>

        </form>
    </div>

    <style>
        /* Custom styling for Filament components */
        .filament-forms-repeater .filament-forms-repeater-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .filament-forms-repeater .filament-forms-repeater-item:last-child {
            margin-bottom: 0;
        }
        
        .filament-forms-file-upload-component .dropzone {
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 2rem;
            background-color: #f9fafb;
            transition: all 0.3s ease;
            min-height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .filament-forms-file-upload-component .dropzone:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .filament-forms-text-input-component input {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .filament-forms-text-input-component input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .dark .filament-forms-repeater .filament-forms-repeater-item {
            border-color: #374151;
            background-color: #1f2937;
        }
        
        .dark .filament-forms-file-upload-component .dropzone {
            border-color: #4b5563;
            background-color: #111827;
        }
        
        .dark .filament-forms-file-upload-component .dropzone:hover {
            border-color: #3b82f6;
            background-color: #1e3a8a;
        }
        
        .dark .filament-forms-text-input-component input {
            border-color: #4b5563;
            background-color: #1f2937;
            color: #f9fafb;
        }
    </style>
</x-filament-panels::page>