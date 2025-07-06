<x-filament-panels::page>
    <div class="space-y-8">
        {{-- 1. Header Selamat Datang --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10"><div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-16 translate-x-16"></div><div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-12 -translate-x-12"></div></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Selamat Datang, {{ auth()->user()->name }}!</h1>
                    <p class="text-emerald-200 font-mono text-sm tracking-wider">NIK: {{ auth()->user()->nik ?? 'Belum terisi' }}</p>
                    <p class="text-emerald-100 mt-2">Portal Layanan Administrasi Digital Kabupaten Simalungun</p>
                </div>
                <div class="hidden md:block"><div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm"><div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 2) }}</div></div></div>
            </div>
        </div>

        {{-- 2. Alert Verifikasi Akun --}}
        @unless(auth()->user()->verified_at)
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-xl p-6 shadow-sm">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0"><div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div></div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-amber-900">Akun Belum Terverifikasi</h3>
                        <p class="text-amber-700 mt-1">Lengkapi profil Anda untuk dapat mengajukan permohonan dan mengakses semua layanan.</p>
                        <div class="mt-4"><a href="{{ route('filament.warga.pages.profile') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all duration-200 text-sm font-medium shadow-sm hover:shadow-md"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Lengkapi Profil Sekarang</a></div>
                    </div>
                </div>
            </div>
        @endunless

        {{-- 3. Quick Stats (Statistik Utama) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Permohonan Aktif --}}
            <a href="{{ route('filament.warga.resources.permohonans.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-300 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Permohonan Aktif</p>
                        <p class="text-3xl font-bold text-blue-600">{{ auth()->user()->permohonans()->whereNotIn('status', ['selesai', 'ditolak'])->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"><svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                </div>
            </a>
            {{-- Dokumen Selesai --}}
            <a href="{{ route('filament.warga.resources.permohonans.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-emerald-300 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Dokumen Selesai</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ auth()->user()->permohonans()->where('status', 'selesai')->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"><svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                </div>
            </a>
            {{-- Tiket Bantuan --}}
            <a href="{{ route('filament.warga.resources.tickets.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-purple-300 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Tiket Bantuan</p>
                        <p class="text-3xl font-bold text-purple-600">{{ auth()->user()->tickets()->whereIn('status', ['open', 'in_progress'])->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"><svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
                </div>
            </a>
            {{-- Pesan Belum Dibaca --}}
            <a href="{{ route('filament.warga.resources.tickets.index') }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-red-300 transition-all duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pesan Belum Dibaca</p>
                        <p class="text-3xl font-bold text-red-600">{{ $this->unreadMessagesCount }}</p>
                    </div>
                    <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"><svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                </div>
            </a>
        </div>

        {{-- 4. Layanan Tersedia --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6"><h2 class="text-xl font-bold text-gray-900 flex items-center"><svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"></path></svg>Layanan Tersedia</h2><a href="{{ url('warga/pilih-layanan') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium flex items-center">Lihat Semua <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></div>
            
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->availableServices as $layanan)
                    <a href="{{ route('filament.warga.resources.permohonans.create', ['layanan_id' => $layanan->id]) }}" class="group p-4 border border-gray-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200 cursor-pointer">
                        <div class="flex flex-col sm:flex-row items-center text-center sm:text-left sm:space-x-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors duration-200 flex-shrink-0 mb-3 sm:mb-0">
                                <x-icon :name="$layanan->icon ?? 'heroicon-o-document-text'" class="w-6 h-6 text-emerald-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors duration-200 leading-tight">{{ $layanan->name }}</h3>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500"><p>Saat ini belum ada layanan yang tersedia.</p></div>
                @endforelse
            </div>
        </div>

        {{-- 5. Permohonan Terbaru & Informasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6"><h2 class="text-xl font-bold text-gray-900">Status Permohonan Terbaru</h2><a href="{{ route('filament.warga.resources.permohonans.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">Lihat Semua</a></div>
                @php
                    $recentPermohonans = auth()->user()->permohonans()->with('layanan')->latest()->take(4)->get();
                @endphp
                @if($recentPermohonans->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($recentPermohonans as $permohonan)
                            <a href="{{ route('filament.warga.resources.permohonans.view', $permohonan) }}" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors duration-200 group">
                                <div class="flex items-center space-x-4 flex-1 min-w-0">
                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition-colors duration-200 flex-shrink-0"><x-icon :name="$permohonan->layanan->icon ?? 'heroicon-o-document-text'" class="w-6 h-6 text-gray-600" /></div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $permohonan->data_pemohon['jenis_permohonan'] ?? 'Permohonan' }}</h3>
                                        <p class="text-sm text-gray-500 truncate">{{ $permohonan->layanan->name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 flex-shrink-0 ml-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ match ($permohonan->status) {'selesai' => 'bg-green-100 text-green-800', 'ditolak' => 'bg-red-100 text-red-800', 'membutuhkan_revisi' => 'bg-amber-100 text-amber-800', 'diproses' => 'bg-blue-100 text-blue-800', default => 'bg-gray-100 text-gray-800'} }}">{{ \App\Models\Permohonan::STATUS_OPTIONS[$permohonan->status] ?? $permohonan->status }}</span>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12"><div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Permohonan</h3><p class="text-gray-500 mb-6">Mulai ajukan permohonan pertama Anda untuk melihat statusnya di sini.</p><a href="{{ route('layanan.semua') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors duration-200 font-medium"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>Ajukan Permohonan</a></div>
                @endif
            </div>

            <div class="bg-gray-100 rounded-2xl p-6 space-y-6">
                <div><h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center"><svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Informasi Penting</h3><div class="space-y-3 text-gray-700 text-sm"><p><strong>Jam Operasional:</strong><br>Senin - Jumat: 08:00 - 16:00 WIB</p><p><strong>Call Center:</strong><br>0821-xxxx-xxxx (24 Jam)</p></div></div>
                <div><h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center"><svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Tips & Bantuan</h3><div class="space-y-3 text-gray-700 text-sm"><p>Pastikan semua dokumen sudah dalam format digital (PDF/JPG) sebelum mengajukan permohonan.</p><a href="{{ route('kb.index') }}" class="font-semibold text-emerald-600 hover:underline">Kunjungi Pusat Bantuan →</a></div></div>
            </div>
        </div>
    </div>
</x-filament-panels::page>