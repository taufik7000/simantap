<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SImantap - Simalungun Administrasi Terpadu</title>
    @vite('resources/css/app.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeIn 0.8s ease forwards;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .pulse-glow {
            animation: pulse-glow 2.5s infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 25px rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.7); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-lg">S</span>
                            </div>
                            <span class="ml-3 text-xl font-bold text-gray-800">SImantap</span>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Beranda</a>
                        <a href="#layanan" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Layanan</a>
                        <a href="#tentang" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Tentang</a>
                        <a href="#kontak" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Kontak</a>
                        <a href="{{ route('login') }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Masuk
                        </a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button class="text-gray-700 hover:text-primary-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <section id="home" class="pt-20 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 min-h-screen flex items-center overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-pulse animation-delay-4000"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
                        Administrasi
                        <span class="block text-primary-200">Terpadu</span>
                        <span class="block text-3xl lg:text-4xl font-semibold">Simalungun</span>
                    </h1>
                    <p class="text-xl text-primary-100 mb-8 leading-relaxed">
                        Layanan administrasi digital yang mudah, cepat, dan terpercaya untuk masyarakat Kabupaten Simalungun.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-4 rounded-lg font-semibold hover:bg-primary-50 transition-colors transform hover:scale-105 shadow-lg">
                            Daftar Sekarang
                        </a>
                        <a href="#layanan" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-primary-700 transition-colors">
                            Lihat Layanan
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="float-animation">
                        <div class="bg-white/20 backdrop-blur-lg rounded-2xl shadow-2xl p-8 transform rotate-3">
                            <div class="bg-primary-600 rounded-lg p-6 mb-6 shadow-inner">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-white font-semibold">Dashboard SImantap</h3>
                                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-white bg-opacity-20 rounded p-3">
                                        <div class="flex justify-between items-center text-white text-sm">
                                            <span>Surat Keterangan</span>
                                            <span class="text-green-300 font-bold">✓ Selesai</span>
                                        </div>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded p-3">
                                        <div class="flex justify-between items-center text-white text-sm">
                                            <span>Izin Usaha</span>
                                            <span class="text-yellow-300 font-bold">⏳ Diproses</span>
                                        </div>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded p-3">
                                        <div class="flex justify-between items-center text-white text-sm">
                                            <span>Perpanjang KTP</span>
                                            <span class="text-red-300 font-bold">! Butuh Aksi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih SImantap?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Platform digital yang mengintegrasikan seluruh layanan administrasi pemerintahan untuk kemudahan masyarakat.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-hover bg-primary-50 rounded-2xl p-8 text-center border border-primary-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Proses Cepat</h3>
                    <p class="text-gray-600">Layanan administrasi yang dapat diproses dalam hitungan menit, bukan hari.</p>
                </div>
                
                <div class="card-hover bg-primary-50 rounded-2xl p-8 text-center border border-primary-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Terpercaya</h3>
                    <p class="text-gray-600">Sistem keamanan tingkat tinggi dengan enkripsi data end-to-end.</p>
                </div>
                
                <div class="card-hover bg-primary-50 rounded-2xl p-8 text-center border border-primary-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Mudah Digunakan</h3>
                    <p class="text-gray-600">Interface yang user-friendly, dapat diakses dari berbagai perangkat.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Layanan Tersedia</h2>
                <p class="text-xl text-gray-600">Berbagai layanan administrasi dalam satu platform</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4"><svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                    <h3 class="font-semibold text-gray-900 mb-2">Surat Keterangan</h3>
                    <p class="text-gray-600 text-sm">SKCK, Surat Domisili, Surat Keterangan Usaha</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4"><svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg></div>
                    <h3 class="font-semibold text-gray-900 mb-2">Dokumen Identitas</h3>
                    <p class="text-gray-600 text-sm">KTP, Kartu Keluarga, Akta Kelahiran</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4"><svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                    <h3 class="font-semibold text-gray-900 mb-2">Perizinan Usaha</h3>
                    <p class="text-gray-600 text-sm">SIUP, TDP, Izin Mendirikan Bangunan</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg card-hover">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4"><svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <h3 class="font-semibold text-gray-900 mb-2">Pajak & Retribusi</h3>
                    <p class="text-gray-600 text-sm">PBB, Pajak Kendaraan, Retribusi Daerah</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-primary-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center text-white">
                <div><div class="text-4xl font-bold mb-2">15,000+</div><div class="text-primary-200">Pengguna Aktif</div></div>
                <div><div class="text-4xl font-bold mb-2">25+</div><div class="text-primary-200">Jenis Layanan</div></div>
                <div><div class="text-4xl font-bold mb-2">98%</div><div class="text-primary-200">Tingkat Kepuasan</div></div>
                <div><div class="text-4xl font-bold mb-2">24/7</div><div class="text-primary-200">Akses Online</div></div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">Siap Memulai?</h2>
            <p class="text-xl text-primary-200 mb-8">Bergabunglah dengan ribuan warga Simalungun yang telah merasakan kemudahan layanan administrasi digital.</p>
            <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors transform hover:scale-105 shadow-xl pulse-glow">
                Daftar Sekarang
            </a>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center"><span class="text-white font-bold text-lg">S</span></div>
                        <span class="ml-3 text-xl font-bold">SImantap</span>
                    </div>
                    <p class="text-gray-400 text-sm">Melayani masyarakat Simalungun dengan teknologi terdepan untuk administrasi yang lebih baik.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Layanan</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Surat Keterangan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Dokumen Identitas</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Perizinan Usaha</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pajak & Retribusi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Bantuan</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Panduan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <div class="space-y-2 text-gray-400 text-sm">
                        <p>Jl. Sisingamangaraja No. 1, Pematang Siantar</p>
                        <p>Kabupaten Simalungun, Sumatera Utara 21118</p>
                        <p>Telp: (0622) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; 2025 SImantap - Pemerintah Kabupaten Simalungun. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>