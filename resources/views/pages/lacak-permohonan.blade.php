<x-layouts.app>
    <x-slot name="title">Lacak Status Permohonan</x-slot>

    <div class="bg-gradient-to-br from-slate-50 via-primary-50/30 to-slate-50 py-12 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Hero Section --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-lg mb-6">
                    <x-heroicon-o-magnifying-glass class="w-8 h-8 text-white" />
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900 mb-4">
                    Lacak Status Permohonan
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Pantau perkembangan permohonan Anda secara real-time dengan memasukkan kode unik yang telah diberikan.
                </p>
            </div>

            {{-- Informasi Cara Pelacakan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">1. Siapkan Kode</h3>
                    <p class="text-gray-600 text-sm">Pastikan Anda memiliki kode permohonan yang diterima saat pendaftaran via email atau SMS.</p>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6 text-primary-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">2. Masukkan Kode</h3>
                    <p class="text-gray-600 text-sm">Ketikkan kode permohonan pada form pencarian di bawah ini dengan benar.</p>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <x-heroicon-o-eye class="w-6 h-6 text-green-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">3. Lihat Status</h3>
                    <p class="text-gray-600 text-sm">Dapatkan informasi lengkap tentang status dan riwayat permohonan Anda.</p>
                </div>
            </div>
            
            {{-- Form Pencarian --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 p-8 mb-8">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Masukkan Kode Permohonan</h2>
                        <p class="text-gray-600">
                            Kode permohonan biasanya dimulai dengan huruf dan diikuti angka, contoh: <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">SP6J2024001</span>
                        </p>
                    </div>
                    
                    <form action="{{ route('lacak.permohonan') }}" method="GET" class="space-y-6">
                        <div>
                            <label for="kode_permohonan" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Permohonan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <x-heroicon-o-hashtag class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" 
                                       name="kode_permohonan" 
                                       id="kode_permohonan"
                                       class="block w-full pl-12 pr-4 py-4 border-gray-300 rounded-xl shadow-sm placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base transition-all duration-200"
                                       placeholder="Masukkan kode permohonan (contoh: SP6J2024001)"
                                       value="{{ request('kode_permohonan') }}"
                                       required
                                       autocomplete="off">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                <x-heroicon-o-information-circle class="inline w-4 h-4 mr-1" />
                                Tidak menemukan kode? Periksa email atau SMS konfirmasi pendaftaran Anda.
                            </p>
                        </div>
                        
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                            Lacak Permohonan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Tampilkan Hasil Pencarian --}}
            @if(request('kode_permohonan'))
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    @if($permohonan)
                        {{-- Header Hasil --}}
                        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Permohonan Ditemukan</h2>
                                    <p class="text-primary-100 mt-1">Kode: {{ $permohonan->kode_permohonan }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{
                                        match ($permohonan->status) {
                                            'selesai', 'disetujui' => 'bg-green-100 text-green-800',
                                            'ditolak', 'membutuhkan_revisi' => 'bg-red-100 text-red-800',
                                            'diproses', 'sedang_ditinjau' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        }
                                    }}">
                                        <div class="w-2 h-2 rounded-full mr-2 {{
                                            match ($permohonan->status) {
                                                'selesai', 'disetujui' => 'bg-green-500',
                                                'ditolak', 'membutuhkan_revisi' => 'bg-red-500',
                                                'diproses', 'sedang_ditinjau' => 'bg-blue-500',
                                                default => 'bg-gray-500',
                                            }
                                        }}"></div>
                                        {{ \App\Models\Permohonan::STATUS_OPTIONS[$permohonan->status] ?? $permohonan->status }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            {{-- Informasi Permohonan --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                                <div class="space-y-6">
                                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                        <x-heroicon-o-document-text class="inline w-5 h-5 mr-2" />
                                        Detail Permohonan
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-1">Jenis Layanan</p>
                                            <p class="font-semibold text-gray-900">{{ $permohonan->layanan->name }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-1">Jenis Permohonan</p>
                                            <p class="font-semibold text-gray-900">{{ $permohonan->data_pemohon['jenis_permohonan'] ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-1">Tanggal Diajukan</p>
                                            <p class="font-semibold text-gray-900">{{ $permohonan->created_at->format('d F Y, H:i') }} WIB</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                                        <x-heroicon-o-clock class="inline w-5 h-5 mr-2" />
                                        Estimasi Waktu
                                    </h3>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" />
                                            <div>
                                                <p class="text-sm font-medium text-blue-900 mb-1">Estimasi Selesai</p>
                                                <p class="text-sm text-blue-700">
                                                    Berdasarkan SLA layanan, permohonan ini diperkirakan selesai dalam 
                                                    <span class="font-semibold">{{ $permohonan->layanan->estimasi_waktu ?? '7 hari kerja' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($permohonan->status === 'membutuhkan_revisi')
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                            <div class="flex items-start">
                                                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-500 mr-3 mt-0.5 flex-shrink-0" />
                                                <div>
                                                    <p class="text-sm font-medium text-amber-900 mb-1">Perlu Tindakan</p>
                                                    <p class="text-sm text-amber-700">
                                                        Permohonan Anda membutuhkan revisi atau dokumen tambahan. Silakan periksa catatan di riwayat status.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">Progress Permohonan</h3>
                                    <span class="text-sm text-gray-500">
                                        @php
                                            $progress = match($permohonan->status) {
                                                'diajukan' => 25,
                                                'diproses', 'sedang_ditinjau' => 50,
                                                'membutuhkan_revisi' => 40,
                                                'disetujui' => 75,
                                                'selesai' => 100,
                                                'ditolak' => 100,
                                                default => 25
                                            };
                                        @endphp
                                        {{ $progress }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-500 {{
                                        match($permohonan->status) {
                                            'selesai', 'disetujui' => 'bg-gradient-to-r from-green-500 to-green-600',
                                            'ditolak' => 'bg-gradient-to-r from-red-500 to-red-600',
                                            'membutuhkan_revisi' => 'bg-gradient-to-r from-amber-500 to-amber-600',
                                            default => 'bg-gradient-to-r from-primary-500 to-primary-600'
                                        }
                                    }}" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Riwayat / Timeline --}}
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 border-b border-gray-200 pb-2">
                                    <x-heroicon-o-clock class="inline w-5 h-5 mr-2" />
                                    Riwayat Status Permohonan
                                </h3>
                                
                                <div class="relative">
                                    @foreach($permohonan->logs->reverse() as $index => $log)
                                    <div class="relative flex items-start pb-8 {{ !$loop->last ? 'border-l-2 border-gray-200 ml-4' : '' }}">
                                        <div class="absolute -left-2 top-2 w-4 h-4 rounded-full border-4 border-white shadow-sm {{
                                            match($log->status) {
                                                'selesai', 'disetujui' => 'bg-green-500',
                                                'ditolak' => 'bg-red-500',
                                                'membutuhkan_revisi' => 'bg-amber-500',
                                                'diproses', 'sedang_ditinjau' => 'bg-blue-500',
                                                default => 'bg-primary-500'
                                            }
                                        }}"></div>
                                        
                                        <div class="ml-8 bg-gray-50 rounded-lg p-4 w-full">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-semibold text-gray-900">
                                                    {{ \App\Models\Permohonan::STATUS_OPTIONS[$log->status] ?? $log->status }}
                                                </h4>
                                                <span class="text-sm text-gray-500">
                                                    {{ $log->created_at->format('d M Y, H:i') }} WIB
                                                </span>
                                            </div>
                                            @if($log->catatan)
                                                <p class="text-gray-700 text-sm leading-relaxed">{{ $log->catatan }}</p>
                                            @endif
                                            
                                            @if($index === 0)
                                                <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                    <div class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-1.5"></div>
                                                    Status Terkini
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button onclick="window.print()" 
                                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                        <x-heroicon-o-printer class="w-5 h-5 mr-2" />
                                        Cetak Status
                                    </button>
                                    
                                    @if($permohonan->status === 'membutuhkan_revisi')
                                        <a href="#" 
                                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 transition-all duration-200">
                                            <x-heroicon-o-pencil-square class="w-5 h-5 mr-2" />
                                            Revisi Permohonan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Permohonan Tidak Ditemukan --}}
                        <div class="text-center py-16 px-8">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <x-heroicon-o-folder-open class="w-12 h-12 text-gray-400" />
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Permohonan Tidak Ditemukan</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Kode permohonan yang Anda masukkan tidak dapat ditemukan dalam sistem kami. 
                                Mohon periksa kembali kode yang Anda masukkan.
                            </p>
                            
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 max-w-md mx-auto">
                                <div class="flex items-start">
                                    <x-heroicon-o-light-bulb class="w-5 h-5 text-amber-500 mr-3 mt-0.5 flex-shrink-0" />
                                    <div class="text-left">
                                        <p class="text-sm font-medium text-amber-900 mb-2">Tips Pencarian:</p>
                                        <ul class="text-sm text-amber-800 space-y-1">
                                            <li>• Pastikan tidak ada spasi di awal atau akhir kode</li>
                                            <li>• Periksa huruf besar/kecil pada kode</li>
                                            <li>• Cek kembali email/SMS konfirmasi</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <button onclick="document.getElementById('kode_permohonan').focus()" 
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                                <x-heroicon-o-arrow-up class="w-5 h-5 mr-2" />
                                Coba Lagi
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- FAQ Section --}}
            <div class="mt-12 bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">
                    <x-heroicon-o-question-mark-circle class="inline w-6 h-6 mr-2" />
                    Pertanyaan Yang Sering Diajukan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Dimana saya bisa mendapatkan kode permohonan?</h4>
                            <p class="text-sm text-gray-600">Kode permohonan dikirimkan melalui email atau SMS setelah Anda berhasil mengajukan permohonan.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Berapa lama proses permohonan?</h4>
                            <p class="text-sm text-gray-600">Waktu proses bervariasi tergantung jenis layanan, umumnya 3-14 hari kerja.</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Apa yang harus dilakukan jika status "Membutuhkan Revisi"?</h4>
                            <p class="text-sm text-gray-600">Periksa catatan di riwayat status dan lengkapi dokumen yang diminta sesuai petunjuk.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Bagaimana jika kode tidak ditemukan?</h4>
                            <p class="text-sm text-gray-600">Pastikan kode diketik dengan benar atau hubungi layanan pelanggan untuk bantuan.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>