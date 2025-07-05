<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Header Halaman --}}
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $this->layanan->name }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Pilih salah satu jenis permohonan di bawah ini yang sesuai dengan kebutuhan Anda. Pastikan Anda membaca deskripsi dan persyaratan dengan saksama.
            </p>
        </div>

        {{-- Form Utama --}}
        <x-filament-panels::form wire:submit="create">
            {{-- Menyimpan form fields dari Filament --}}
            {{ $this->form }}

            {{-- Pilihan Jenis Permohonan dalam bentuk Card --}}
            <div class="mt-6">
                <x-filament::input.label>Pilih Jenis Permohonan</x-filament::input.label>
                <div class="mt-2 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->jenisPermohonanData as $jenis)
                        <div @click="$wire.set('data.data_pemohon.jenis_permohonan', '{{ $jenis['nama'] }}')"
                             :class="{
                                'ring-2 ring-primary-600 dark:ring-primary-500 border-transparent': $wire.data.data_pemohon.jenis_permohonan === '{{ $jenis['nama'] }}',
                                'border-gray-300 dark:border-gray-700': $wire.data.data_pemohon.jenis_permohonan !== '{{ $jenis['nama'] }}'
                             }"
                             class="relative flex flex-col rounded-lg border bg-white dark:bg-gray-800 p-6 shadow-sm cursor-pointer transition-all duration-200 hover:shadow-lg hover:border-primary-500">

                            {{-- Radio Button Tersembunyi --}}
                            <input type="radio" name="jenis_permohonan" value="{{ $jenis['nama'] }}" class="sr-only">

                            {{-- Tanda Centang jika terpilih --}}
                            <div :class="{ 'opacity-100': $wire.data.data_pemohon.jenis_permohonan === '{{ $jenis['nama'] }}', 'opacity-0': $wire.data.data_pemohon.jenis_permohonan !== '{{ $jenis['nama'] }}' }"
                                 class="absolute top-4 right-4 text-primary-600 transition-opacity">
                                <x-heroicon-s-check-circle class="h-6 w-6" />
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $jenis['nama'] }}</h3>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                    {!! $jenis['deskripsi'] !!}
                                </div>
                                
                                {{-- Menampilkan Formulir yang perlu diunduh --}}
                                @if(!empty($jenis['nama_formulir']))
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <h4 class="font-medium text-sm text-gray-700 dark:text-gray-300">Formulir untuk Diunduh:</h4>
                                        <ul class="mt-1 list-disc list-inside space-y-1">
                                            @foreach($jenis['nama_formulir'] as $index => $nama)
                                                <li>
                                                    <a href="{{ route('formulir.download', ['formulirMaster' => $jenis['formulir_master_id'][$index]]) }}" target="_blank" class="text-primary-600 hover:underline">
                                                        {{ $nama }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tombol Aksi Form --}}
            <div class="mt-8">
                <x-filament-panels::form.actions 
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                /> 
            </div>
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>