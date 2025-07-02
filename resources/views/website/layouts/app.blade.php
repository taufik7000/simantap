<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->meta_title ?? $content->title ?? 'SIMANTAP' }}</title>
    <meta name="description" content="{{ $content->meta_description ?? 'Platform administrasi digital Kabupaten Simalungun' }}">
    
    @if($content->featured_image)
    <meta property="og:image" content="{{ Storage::url($content->featured_image) }}">
    @endif
    
    <meta property="og:title" content="{{ $content->meta_title ?? $content->title }}">
    <meta property="og:description" content="{{ $content->meta_description }}">
    <meta property="og:type" content="website">
    
    @vite('resources/css/app.css')
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-lg">S</span>
                            </div>
                            <span class="ml-3 text-xl font-bold text-gray-800">SIMANTAP</span>
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="{{ route('website.home') }}" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Beranda</a>
                        <a href="{{ route('website.page', 'tentang-kami') }}" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Tentang</a>
                        <a href="{{ route('website.page', 'kontak') }}" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Kontak</a>
                        <a href="{{ route('login') }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">S</span>
                        </div>
                        <span class="ml-3 text-xl font-bold">SIMANTAP</span>
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
                        <p>{{ $content->getContentValue('office_address', 'Jl. Sisingamangaraja No. 1, Pematang Siantar') }}</p>
                        <p>Telp: {{ $content->getContentValue('phone', '(0622) 123-4567') }}</p>
                        <p>Email: {{ $content->getContentValue('email', 'info@simantap.simalungunkab.go.id') }}</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SIMANTAP - Pemerintah Kabupaten Simalungun. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>