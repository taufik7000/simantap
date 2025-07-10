<x-layouts.app>
    <x-slot name="title">Lacak Status Permohonan</x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl mb-4">
                    <x-heroicon-o-magnifying-glass class="w-6 h-6 text-white" />
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Lacak Status Permohonan
                </h1>
                <p class="text-gray-600 text-sm sm:text-base max-w-2xl mx-auto">
                    Pantau perkembangan permohonan Anda dengan memasukkan kode unik yang telah diberikan.
                </p>
            </div>
            
            {{-- Search Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <form action="{{ route('lacak.permohonan') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="kode_permohonan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Permohonan
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-hashtag class="h-5 w-5 text-gray-400" />
                            </div>
                            <input type="text" 
                                   name="kode_permohonan" 
                                   id="kode_permohonan"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition-colors"
                                   placeholder="Masukkan kode permohonan"
                                   value="{{ request('kode_permohonan') }}"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 mr-2" />
                        Lacak Permohonan
                    </button>
                </form>
            </div>

            {{-- Search Results --}}
            @if(request('kode_permohonan'))
                @if($permohonan)
                    {{-- Application Found --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        {{-- Header --}}
                        <div class="bg-primary-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                        <x-heroicon-o-document-check class="w-5 h-5 text-white" />
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-semibold text-white">{{ $permohonan->kode_permohonan }}</h2>
                                        <p class="text-primary-100 text-sm">
                                            Permohonan ditemukan
                                        </p>
                                    </div>
                                </div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium animate-bounce 
                                 bg-{{ $permohonan->status->getColor() }}-100 text-{{ $permohonan->status->getColor() }}-800">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 animate-pulse 
                                bg-{{ $permohonan->status->getColor() }}-600">
                                </span>
                                {{ $permohonan->status->getLabel() }}
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            {{-- Application Details --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-start space-x-2">
                                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-gray-500 mt-1 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Jenis Permohonan</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $permohonan->data_pemohon['jenis_permohonan'] ?? $permohonan->jenis_permohonan ?? 'Belum ditentukan' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-gray-500 mt-1 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Tanggal Dibuat</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $permohonan->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <x-heroicon-o-user class="w-4 h-4 text-gray-500 mt-1 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pemohon</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $permohonan->nama_pemohon ?? $permohonan->user->name ?? 'Tidak diketahui' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2">
                                    <x-heroicon-o-clock class="w-4 h-4 text-gray-500 mt-1 flex-shrink-0" />
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Estimasi Selesai</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1">
                                            @if($permohonan->estimasi_selesai)
                                                {{ $permohonan->estimasi_selesai->format('d M Y') }}
                                            @else
                                                {{ $permohonan->created_at->addDays(7)->format('d M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress</span>
                                    <span class="text-sm text-gray-500">
                                        @php
                                            $progress = match($permohonan->status) {
                                                'baru' => 25,
                                                'verifikasi_berkas', 'diperbaiki_warga' => 40,
                                                'menunggu_entri_data' => 50,
                                                'proses_entri', 'entri_data_selesai' => 60,
                                                'menunggu_persetujuan' => 75,
                                                'disetujui', 'dokumen_diterbitkan' => 90,
                                                'selesai' => 100,
                                                'ditolak' => 100,
                                                'butuh_perbaikan' => 30,
                                                default => 10
                                            };
                                        @endphp
                                        {{ $progress }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 {{
                                        match($permohonan->status) {
                                            'selesai', 'disetujui' => 'bg-green-500',
                                            'ditolak' => 'bg-red-500',
                                            'butuh_perbaikan' => 'bg-amber-500',
                                            default => 'bg-primary-500'
                                        }
                                    }}" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Timeline --}}
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                                    <x-heroicon-o-clock class="w-4 h-4 mr-2" />
                                    Riwayat Status
                                </h3>
                                
                                <div class="space-y-4">
                                    @forelse($permohonan->logs->sortByDesc('created_at') as $log)
                                        <div class="flex items-start space-x-3">
                                            {{-- Status Icon --}}
                                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center {{
                                                match(true) {
                                                    in_array($log->status, ['selesai', 'disetujui', 'dokumen_diterbitkan']) => 'bg-green-100 text-green-600',
                                                    in_array($log->status, ['ditolak', 'butuh_perbaikan']) => 'bg-red-100 text-red-600',
                                                    in_array($log->status, ['verifikasi_berkas', 'menunggu_entri_data', 'proses_entri', 'entri_data_selesai', 'menunggu_persetujuan']) => 'bg-blue-100 text-blue-600',
                                                    default => 'bg-gray-100 text-gray-600'
                                                }
                                            }}">
                                                <x-dynamic-component :component="match(true) {
                                                    in_array($log->status, ['selesai', 'disetujui', 'dokumen_diterbitkan']) => 'heroicon-o-check-circle',
                                                    in_array($log->status, ['ditolak', 'butuh_perbaikan']) => 'heroicon-o-exclamation-triangle',
                                                    default => 'heroicon-o-clock'
                                                }" class="w-3 h-3"/>
                                            </div>
                                            
                                            {{-- Status Content --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $log->status->getLabel() }}
                                                        @if($loop->first)
                                                            <span class="animate-bounce bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded-full ml-2 inline-flex items-center">
                                                                <span class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-1 animate-pulse"></span>
                                                                Terkini
                                                            </span>
                                                        @endif
                                                    </p>
                                                    <time class="text-xs text-gray-500">{{ $log->created_at->format('d M Y, H:i') }}</time>
                                                </div>
                                                @if($log->catatan)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $log->catatan }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-1">oleh {{ $log->user->name ?? 'Sistem' }}</p>
                                            </div>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="ml-3 w-px h-4 bg-gray-200"></div>
                                        @endif
                                    @empty
                                        <div class="text-center py-8 text-gray-500">
                                            <x-heroicon-o-clock class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                            <p class="text-sm">Belum ada riwayat untuk permohonan ini</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button onclick="window.print()" 
                                            class="flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                                        Cetak Status
                                    </button>
                                    
                                    @if($permohonan->status === 'butuh_perbaikan')
                                        <a href="{{ route('filament.warga.resources.permohonans.view', $permohonan) }}" 
                                           class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-colors">
                                            <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                                            Revisi Permohonan
                                        </a>
                                    @endif

                                    @if(in_array($permohonan->status, ['selesai', 'disetujui', 'dokumen_diterbitkan']))
                                        <button class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                            <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-2" />
                                            Unduh Dokumen
                                        </button>
                                    @endif

                                    <a href="{{ route('lacak.permohonan') }}" 
                                       class="flex items-center justify-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                        Lacak Lainnya
                                    </a>
                                </div>
                            </div>

                            {{-- Help Section --}}
                            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-900 mb-1">Bantuan & Informasi</h4>
                                        <p class="text-sm text-blue-800 mb-2">
                                            Jika Anda memiliki pertanyaan atau membutuhkan bantuan terkait permohonan ini:
                                        </p>
                                        <ul class="text-xs text-blue-700 space-y-1">
                                            <li>• Hubungi layanan pelanggan: <span class="font-medium">(021) 123-4567</span></li>
                                            <li>• Email: <span class="font-medium">layanan@pemda.go.id</span></li>
                                            <li>• Jam operasional: Senin - Jumat, 08:00 - 16:00 WIB</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Application Not Found --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-folder-open class="w-8 h-8 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Permohonan Tidak Ditemukan</h3>
                            <p class="text-gray-600 text-sm mb-6 max-w-sm mx-auto">
                                Kode permohonan yang Anda masukkan tidak dapat ditemukan. Mohon periksa kembali kode yang Anda masukkan.
                            </p>
                            
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 max-w-sm mx-auto">
                                <div class="flex items-start text-left">
                                    <x-heroicon-o-light-bulb class="w-5 h-5 text-amber-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-medium text-amber-900 mb-1">Tips Pencarian:</p>
                                        <ul class="text-xs text-amber-800 space-y-1">
                                            <li>• Pastikan tidak ada spasi di awal atau akhir kode</li>
                                            <li>• Periksa huruf besar/kecil pada kode</li>
                                            <li>• Cek kembali email/SMS konfirmasi</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <button onclick="document.getElementById('kode_permohonan').focus()" 
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                                <x-heroicon-o-arrow-up class="w-4 h-4 mr-2" />
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>