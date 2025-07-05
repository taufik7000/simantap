<x-layouts.app>
    <x-slot name="title">Lacak Status Permohonan</x-slot>

    <div class="bg-slate-50 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Form Pencarian --}}
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="max-w-xl mx-auto text-center">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                        Lacak Status Permohonan Anda
                    </h1>
                    <p class="mt-4 text-gray-600">
                        Masukkan kode permohonan unik yang Anda terima saat pendaftaran untuk melihat progres terbaru.
                    </p>
                </div>
                
                <form action="{{ route('lacak.permohonan') }}" method="GET" class="max-w-xl mx-auto mt-8">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <label for="kode_permohonan" class="sr-only">Kode Permohonan</label>
                        <input type="text" name="kode_permohonan" id="kode_permohonan"
                               class="block w-full px-5 py-3.5 border-gray-300 rounded-lg shadow-sm placeholder-gray-500 focus:ring-emerald-500 focus:border-emerald-500 text-base"
                               placeholder="Contoh: SP6J..."
                               value="{{ request('kode_permohonan') }}"
                               required>
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                            Lacak
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tampilkan Hasil Pencarian --}}
            @if(request('kode_permohonan'))
                <div class="bg-white rounded-2xl shadow-lg p-8 mt-8">
                    @if($permohonan)
                        {{-- Informasi Permohonan Ditemukan --}}
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail Permohonan: {{ $permohonan->kode_permohonan }}</h2>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-base">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Jenis Layanan</p>
                                    <p class="font-semibold text-gray-900">{{ $permohonan->layanan->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Jenis Permohonan</p>
                                    <p class="font-semibold text-gray-900">{{ $permohonan->data_pemohon['jenis_permohonan'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tanggal Diajukan</p>
                                    <p class="font-semibold text-gray-900">{{ $permohonan->created_at->format('d F Y, H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status Terakhir</p>
                                    <p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{
                                            match ($permohonan->status) {
                                                'selesai', 'disetujui' => 'bg-green-100 text-green-800',
                                                'ditolak', 'membutuhkan_revisi' => 'bg-red-100 text-red-800',
                                                'diproses', 'sedang_ditinjau' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            }
                                        }}">
                                            {{ \App\Models\Permohonan::STATUS_OPTIONS[$permohonan->status] ?? $permohonan->status }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <hr class="my-8">

                            {{-- Riwayat / Timeline --}}
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Riwayat Status</h3>
                            <div class="relative border-l-2 border-gray-200 ml-3">
                                <div class="space-y-8">
                                    @foreach($permohonan->logs->reverse() as $log)
                                    <div class="relative">
                                        <div class="absolute -left-[1.3rem] top-1.5 w-6 h-6 bg-emerald-500 rounded-full border-4 border-white"></div>
                                        <div class="pl-10">
                                            <p class="font-semibold text-emerald-600">{{ \App\Models\Permohonan::STATUS_OPTIONS[$log->status] ?? $log->status }}</p>
                                            <p class="text-sm text-gray-500">{{ $log->created_at->format('d F Y, H:i') }}</p>
                                            <p class="mt-1 text-gray-700">{{ $log->catatan }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Permohonan Tidak Ditemukan --}}
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Permohonan Tidak Ditemukan</h3>
                            <p class="mt-1 text-sm text-gray-500">Pastikan kode yang Anda masukkan sudah benar dan coba lagi.</p>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>