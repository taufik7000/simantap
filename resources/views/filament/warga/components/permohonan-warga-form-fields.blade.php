@php
    $selectedJenis = $get('data_pemohon.jenis_permohonan');
    $layanan = $this->layanan;
    $formFields = [];

    if ($selectedJenis && $layanan) {
        $jenisData = collect($layanan->description)->firstWhere('nama_syarat', $selectedJenis);
        if ($jenisData && !empty($jenisData['form_fields'])) {
            $formFields = $jenisData['form_fields'];
        }
    }
@endphp

{{-- Hanya tampilkan jika jenis permohonan sudah dipilih DAN ada form fields yang harus diisi --}}
@if ($selectedJenis && !empty($formFields))
    <div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Formulir Isian: {{ $selectedJenis }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Silakan isi data yang diperlukan di bawah ini.</p>
        
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            {{-- Loop untuk setiap field yang didefinisikan Kadis --}}
            @foreach ($formFields as $field)
                <div class="sm:col-span-{{ $field['field_type'] === 'textarea' ? '6' : '3' }}">
                    @php
                        // Nama field untuk wire:model, misal: data_pemohon_dinamis.nama_lengkap
                        $wireModelName = 'data.data_pemohon_dinamis.' . $field['field_name'];
                    @endphp
                    
                    <x-filament::input.label for="{{ $field['field_name'] }}">
                        {{ $field['field_label'] }}
                        @if ($field['is_required'])
                            <span class="text-danger-600">*</span>
                        @endif
                    </x-filament::input.label>
                    
                    <div class="mt-1">
                        @switch($field['field_type'])
                            @case('textarea')
                                <x-filament::input.textarea id="{{ $field['field_name'] }}" wire:model.blur="{{ $wireModelName }}" />
                                @break

                            @case('select')
                                <x-filament::input.select id="{{ $field['field_name'] }}" wire:model.live="{{ $wireModelName }}">
                                    <option value="">Pilih salah satu</option>
                                    @foreach($field['field_options'] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </x-filament::input.select>
                                @break

                            @default
                                <x-filament::input.text id="{{ $field['field_name'] }}" type="{{ $field['field_type'] }}" wire:model.blur="{{ $wireModelName }}" />
                        @endswitch
                    </div>

                    @error($wireModelName)
                        <x-filament::input.error :messages="$message" class="mt-2" />
                    @enderror
                </div>
            @endforeach
        </div>
    </div>
@endif