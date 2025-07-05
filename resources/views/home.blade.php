<x-layouts.app>
    {{-- Semua kode <section> Anda dari file lama ditempatkan di sini --}}
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-50 via-emerald-50 to-emerald-100">
        <div class="absolute inset-0 grid-pattern opacity-30"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-emerald-300/20 to-emerald-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-r from-emerald-400/20 to-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-r from-emerald-300/20 to-emerald-400/20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-emerald-600/10 to-emerald-700/10 border border-emerald-200/50 backdrop-blur-sm mb-8">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">Platform Administrasi Digital Terdepan</span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-bold mb-8 leading-tight">
                        <span class="gradient-text">Simalungun</span>
                        <br>
                        <span class="text-gray-800">Administrasi Terpadu</span>
                    </h1>
                    
                    <p class="text-xl lg:text-2xl text-gray-600 mb-12 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Layanan administrasi digital yang mudah dan cepat untuk masyarakat Kabupaten Simalungun.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="/register" class="group relative px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 to-emerald-800 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="relative flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                Mulai Sekarang
                            </span>
                        </a>
                        <a href="/semua-layanan" class="group px-8 py-4 bg-white/80 backdrop-blur-sm text-gray-700 font-semibold rounded-2xl border border-gray-200/50 hover:bg-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                             <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a3 3 0 013-3h6a3 3 0 013 3v2M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Lihat Layanan
                            </span>
                        </a>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-8 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            100% Secure
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            24/7 Support
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            ISO Certified
                        </div>
                    </div>
                </div>
                
                <div id="demo" class="relative">
                     <div class="floating-card glassmorphism rounded-3xl p-8 shadow-2xl border border-white/20">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-lg">S</span>
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-bold text-gray-800">SIMANTAP Dashboard</h3>
                                    <p class="text-sm text-gray-500">Portal Administrasi</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span class="text-xs text-gray-600 font-medium">Online</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-2xl p-4 border border-emerald-200/50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-emerald-600 text-sm font-medium">Dokumen Selesai</p>
                                        <p class="text-2xl font-bold text-emerald-700">83,247</p>
                                    </div>
                                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-2xl p-4 border border-teal-200/50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-teal-600 text-sm font-medium">Proses Aktif</p>
                                        <p class="text-2xl font-bold text-teal-700">389</p>
                                    </div>
                                    <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-700 text-sm">Aktivitas Terbaru</h4>
                            <div class="space-y-3">
                                <div class="flex items-center p-3 bg-white/60 rounded-xl border border-gray-100/50">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Surat Keterangan Domisili</p>
                                        <p class="text-xs text-gray-500">Selesai • 2 menit lalu</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white/60 rounded-xl border border-gray-100/50">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Perpanjangan KTP</p>
                                        <p class="text-xs text-gray-500">Dalam proses • 1 jam lalu</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 bg-white/60 rounded-xl border border-gray-100/50">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Izin Usaha Mikro</p>
                                        <p class="text-xs text-gray-500">Menunggu verifikasi • 3 jam lalu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-r from-emerald-300 to-emerald-400 rounded-2xl opacity-80 floating-card" style="animation-delay: -2s;"></div>
                    <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-2xl opacity-80 floating-card" style="animation-delay: -4s;"></div>
                </div>
            </div>
        </div>
    </section>

   <section class="py-24 bg-white scroll-reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="layanan-populer" class="text-center mb-20">
                <div class="inline-flex items-center gap-x-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 mb-6">
                    <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                    <span class="text-sm font-medium text-emerald-700">Layanan Utama</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Layanan <span class="gradient-text">Paling Dicari</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed mb-8">
                    Akses cepat ke layanan administrasi yang paling sering digunakan oleh masyarakat Simalungun.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                {{-- KARTU KTP --}}
                <div class="group relative"><div class="h-full bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-emerald-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"><div class="relative mb-8"><div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"><x-icon name="heroicon-o-identification" class="w-8 h-8 text-white" /></div><div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full opacity-70 group-hover:scale-125 transition-transform duration-300"></div></div><h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-emerald-600 transition-colors duration-300">KTP (Kartu Tanda Penduduk)</h3><p class="text-gray-600 leading-relaxed mb-6">Permohonan pembuatan, perpanjangan, dan perubahan data KTP elektronik.</p><a href="/layanan/ktp" class="inline-flex items-center text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">Ajukan Sekarang<svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></div></div>
                {{-- KARTU KARTU KELUARGA --}}
                <div class="group relative"><div class="h-full bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-emerald-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"><div class="relative mb-8"><div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"><x-icon name="heroicon-o-user-group" class="w-8 h-8 text-white" /></div><div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full opacity-70 group-hover:scale-125 transition-transform duration-300"></div></div><h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-emerald-600 transition-colors duration-300">Kartu Keluarga (KK)</h3><p class="text-gray-600 leading-relaxed mb-6">Pembuatan, perubahan, dan penambahan anggota keluarga dalam Kartu Keluarga.</p><a href="/layanan/kartu-keluarga" class="inline-flex items-center text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">Ajukan Sekarang<svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></div></div>
                {{-- KARTU AKTA KELAHIRAN --}}
                <div class="group relative"><div class="h-full bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-emerald-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"><div class="relative mb-8"><div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"><x-icon name="heroicon-o-document-text" class="w-8 h-8 text-white" /></div><div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full opacity-70 group-hover:scale-125 transition-transform duration-300"></div></div><h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-emerald-600 transition-colors duration-300">Akta Kelahiran</h3><p class="text-gray-600 leading-relaxed mb-6">Penerbitan akta kelahiran untuk bayi baru lahir dan duplikat akta kelahiran.</p><a href="/layanan/akta-kelahiran" class="inline-flex items-center text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">Ajukan Sekarang<svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></div></div>
                {{-- KARTU KIA --}}
                <div class="group relative"><div class="h-full bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-emerald-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"><div class="relative mb-8"><div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300"><x-icon name="heroicon-o-identification" class="w-8 h-8 text-white" /></div><div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full opacity-70 group-hover:scale-125 transition-transform duration-300"></div></div><h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-emerald-600 transition-colors duration-300">Kartu Identitas Anak</h3><p class="text-gray-600 leading-relaxed mb-6">Pembuatan Kartu Identitas Anak (KIA) untuk anak di bawah 17 tahun.</p><a href="/layanan/kartu-identitas-anak" class="inline-flex items-center text-emerald-600 font-semibold hover:text-emerald-700 transition-colors">Ajukan Sekarang<svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a></div></div>
            </div>
            
            <div class="text-center mt-16">
                <a href="{{ route('layanan.semua') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"/></svg>
                    Lihat Semua Layanan
                </a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-gradient-to-br from-slate-900 via-emerald-900 to-emerald-800 relative overflow-hidden scroll-reveal">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-emerald-600/20 to-emerald-700/20"></div>
            <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-r from-emerald-400/20 to-teal-400/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-80 h-80 bg-gradient-to-r from-emerald-500/20 to-green-400/20 rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                    Dipercaya Ribuan <span class="text-emerald-300">Warga</span>
                </h2>
                <p class="text-xl text-emerald-100 max-w-2xl mx-auto">
                    Angka yang membuktikan komitmen kami dalam melayani masyarakat Simalungun
                </p>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div class="glassmorphism rounded-3xl p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="text-4xl lg:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">10.000+</div>
                        <div class="text-emerald-200 font-medium">Pengguna Terdaftar</div>
                    </div>
                </div>
                <div class="text-center group">
                    <div class="glassmorphism rounded-3xl p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-4xl lg:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">83.247+</div>
                        <div class="text-emerald-200 font-medium">Dokumen Diproses</div>
                    </div>
                </div>
                <div class="text-center group">
                    <div class="glassmorphism rounded-3xl p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div class="text-4xl lg:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">98.9%</div>
                        <div class="text-emerald-200 font-medium">Tingkat Kepuasan</div>
                    </div>
                </div>
                <div class="text-center group">
                    <div class="glassmorphism rounded-3xl p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-4xl lg:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">24 jam</div>
                        <div class="text-emerald-200 font-medium">Waktu Proses Rata-rata</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-gray-50 scroll-reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 mb-6">
                    <span class="text-sm font-medium text-emerald-600">💬 Testimoni Pengguna</span>
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Apa Kata <span class="gradient-text">Masyarakat</span>?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Pengalaman nyata dari warga Simalungun yang telah merasakan transformasi digital
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                        </svg>
                    </div>
                    
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "SIMANTAP benar-benar mengubah cara saya mengurus dokumen. Yang dulu butuh berhari-hari, sekarang bisa selesai dalam hitungan jam. Sangat membantu!"
                    </p>
                    
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold">AB</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Ahmad Budiman</h4>
                            <p class="text-sm text-gray-500">Warga Pematang Siantar</p>
                        </div>
                    </div>
                </div>
                
                <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                        </svg>
                    </div>
                    
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "Interface yang mudah dipahami dan prosesnya transparan. Saya bisa tracking status permohonan real-time. Pelayanan yang sangat modern!"
                    </p>
                    
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold">SR</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Sari Ramadhani</h4>
                            <p class="text-sm text-gray-500">Pengusaha UMKM</p>
                        </div>
                    </div>
                </div>
                
                <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                        </svg>
                    </div>
                    
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "Customer service yang responsif dan sistem keamanan yang terjamin. Semua data pribadi terlindungi dengan baik. Sangat recommended!"
                    </p>
                    
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mr-4">
                            <span class="text-white font-bold">DW</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Dewi Wulandari</h4>
                            <p class="text-sm text-gray-500">Karyawan Swasta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 relative overflow-hidden scroll-reveal">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>
        
        <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 mb-8">
                <svg class="w-4 h-4 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-sm font-medium text-white">Bergabung dengan 15,000+ pengguna aktif</span>
            </div>
            
            <h2 class="text-4xl lg:text-6xl font-bold text-white mb-8 leading-tight">
                Siap Memulai<br>
                <span class="text-emerald-200">Transformasi Digital</span>?
            </h2>
            
            <p class="text-xl lg:text-2xl text-emerald-100 mb-12 max-w-3xl mx-auto leading-relaxed">
                Bergabunglah dengan ribuan warga Simalungun yang telah merasakan kemudahan layanan administrasi digital masa depan.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16">
                <a href="/register" class="group relative px-10 py-5 bg-white text-emerald-600 font-bold rounded-2xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-emerald-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative flex items-center justify-center text-lg">
                        <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Daftar Gratis Sekarang
                    </span>
                </a>
            </div>
            
            <div class="flex flex-wrap justify-center items-center gap-12 text-white/80">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-400 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="font-medium">Gratis</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-400 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="font-medium">Aman</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-400 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Dukungan Penuh</span>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>