@php
    // Ambil data dari page-level component (CreatePermohonan.php)
    $jenisPermohonanData = $this->jenisPermohonanData;
@endphp

<div
    x-data="{
        selectedService: null,
        requirements: '',
        selectedFormulirIds: [],
        selectedFormulirNamas: [],
        selectedFormFields: [], 
        
        selectService(service) {
            this.selectedService = service.nama;
            this.requirements = service.deskripsi;
            this.selectedFormulirIds = service.formulir_master_id || [];
            this.selectedFormulirNamas = service.nama_formulir || [];
            this.selectedFormFields = service.form_fields || [];
            
            $wire.set('data.data_pemohon.jenis_permohonan', service.nama); 
        },

        init() {
            const initialLivewireJenisPermohonan = @js($this->data['data_pemohon']['jenis_permohonan'] ?? null);
            if (initialLivewireJenisPermohonan) {
                const initialService = {{ Js::from($jenisPermohonanData) }}.find(service => service.nama === initialLivewireJenisPermohonan);
                if (initialService) {
                    this.selectService(initialService);
                }
            }
        }
    }"
>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-3 space-y-6">
            <x-filament::section>
                <x-slot name="heading">Pilih Jenis Permohonan</x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($jenisPermohonanData as $service)
                        <div
                            @click="selectService(@js($service))"
                            :class="{
                                'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600': selectedService === '{{ $service['nama'] }}',
                                'border-gray-300 dark:border-gray-600 hover:border-primary-500 bg-white dark:bg-gray-800': selectedService !== '{{ $service['nama'] }}'
                            }"
                            class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md border"
                        >
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mr-4">
                               <x-heroicon-s-document-text class="h-6 w-6 text-primary-600" />
                            </div>
                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $service['nama'] }}</h3>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            <div x-show="selectedFormulirIds.length > 0" x-transition>
                <x-filament::section>
                    <x-slot name="heading">Unduh Formulir Wajib</x-slot>
                    <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-4">Layanan ini memerlukan formulir fisik. Silakan unduh, isi, lalu unggah bersama dokumen lainnya.</p>
                    <div class="space-y-3">
                        <template x-for="(formId, index) in selectedFormulirIds" :key="index">
                            <a x-bind:href="`/download-formulir-master/${formId}`" class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:hover:bg-white/10 transition-all duration-200">
                                <x-heroicon-m-arrow-down-tray class="w-5 h-5"/>
                                <span x-text="selectedFormulirNamas[index] ? `Unduh: ${selectedFormulirNamas[index]}` : 'Unduh Formulir'"></span>
                            </a>
                        </template>
                    </div>
                </x-filament::section>
            </div>

            <div x-show="selectedService" x-transition.opacity>
                <x-filament::section>
                    <x-slot name="heading">Deskripsi & Persyaratan</x-slot>
                    <div class="prose max-w-none dark:prose-invert p-6" x-html="requirements"></div>
                </x-filament::section>
            </div>
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-2 space-y-6">
            <div x-show="selectedFormFields.length > 0" x-transition.delay.100ms>
                <x-filament::section>
                    <x-slot name="heading">Formulir Isian</x-slot>
                    <div class="space-y-6">
                        <template x-for="field in selectedFormFields" :key="field.field_name">
                            <div class="space-y-2">
                                <label :for="field.field_name" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white" x-text="field.field_label"></span>
                                    <template x-if="field.is_required"><sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup></template>
                                </label>
                                <div class="fi-input-wrapper flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white focus-within:ring-2 dark:bg-white/5 ring-gray-950/10 focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500">
                                    <template x-if="field.field_type === 'textarea'">
                                        <textarea :id="field.field_name" :name="field.field_name" :required="field.is_required" wire:model.defer="data.data_pemohon_dinamis[field.field_name]" class="min-h-[5rem] block w-full border-none bg-transparent py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white dark:placeholder:text-gray-500"></textarea>
                                    </template>
                                    <template x-if="field.field_type === 'select'">
                                        <select :id="field.field_name" :name="field.field_name" :required="field.is_required" wire:model.defer="data.data_pemohon_dinamis[field.field_name]" class="block w-full border-none bg-transparent py-1.5 pl-3 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white">
                                            <option value="">Pilih salah satu...</option>
                                            <template x-for="option in field.field_options" :key="option.value">
                                                <option :value="option.value" x-text="option.label"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="['text', 'number', 'email', 'date'].includes(field.field_type)">
                                        <input :type="field.field_type" :id="field.field_name" :name="field.field_name" :required="field.is_required" wire:model.defer="data.data_pemohon_dinamis[field.field_name]" class="block w-full border-none bg-transparent py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white">
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </x-filament::section>
            </div>
        </div>
    </div>
</div>