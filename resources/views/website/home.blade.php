@extends('website.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 min-h-screen flex items-center overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <h1 class="text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                    {{ $content->getContentValue('hero_title', 'Administrasi Terpadu Simalungun') }}
                </h1>
                <p class="text-xl text-primary-100 mb-8 leading-relaxed">
                    {{ $content->getContentValue('hero_subtitle', 'Layanan administrasi digital yang mudah, cepat, dan terpercaya untuk masyarakat Kabupaten Simalungun.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-4 rounded-lg font-semibold hover:bg-primary-50 transition-colors transform hover:scale-105 shadow-lg text-center">
                        {{ $content->getContentValue('hero_cta_text', 'Daftar Sekarang') }}
                    </a>
                    <a href="#layanan" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-primary-700 transition-colors text-center">
                        {{ $content->getContentValue('hero_secondary_cta', 'Lihat Layanan') }}
                    </a>
                </div>
            </div>
            <div class="relative">
                <!-- Dashboard Preview -->
                <div class="bg-white/20 backdrop-blur-lg rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    <div class="bg-primary-600 rounded-lg p-6 mb-6 shadow-inner">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-white font-semibold">Dashboard SIMANTAP</h3>
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
</section>

<!-- Features Section -->
@if($content->getContentValue('features'))
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih SIMANTAP?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Platform digital yang mengintegrasikan seluruh layanan administrasi pemerintahan untuk kemudahan masyarakat.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($content->getContentValue('features', []) as $feature)
            <div class="group bg-primary-50 rounded-2xl p-8 text-center border border-primary-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:scale-110 transition-transform">
                    @if(isset($feature['icon']))
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($feature['icon'] === 'heroicon-o-bolt')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            @elseif($feature['icon'] === 'heroicon-o-shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            @elseif($feature['icon'] === 'heroicon-o-device-phone-mobile')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            @endif
                        </svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    @endif
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $feature['title'] ?? 'Fitur' }}</h3>
                <p class="text-gray-600">{{ $feature['description'] ?? 'Deskripsi fitur' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Statistics Section -->
@if($content->getContentValue('statistics'))
<section class="py-20 bg-primary-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center text-white">
            @foreach($content->getContentValue('statistics', []) as $stat)
            <div class="group">
                <div class="text-4xl font-bold mb-2 group-hover:scale-110 transition-transform">{{ $stat['value'] ?? '0' }}</div>
                <div class="text-primary-200">{{ $stat['label'] ?? 'Statistik' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-white mb-6">Siap Memulai?</h2>
        <p class="text-xl text-primary-200 mb-8">Bergabunglah dengan ribuan warga Simalungun yang telah merasakan kemudahan layanan administrasi digital.</p>
        <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors transform hover:scale-105 shadow-xl">
            {{ $content->getContentValue('hero_cta_text', 'Daftar Sekarang') }}
        </a>
    </div>
</section>
@endsection