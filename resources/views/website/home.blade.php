@extends('website.layouts.app')

@push('styles')
<style>
    .glassmorphism {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #3b82f6, #1e40af, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .floating-card {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-10px) rotate(1deg); }
        66% { transform: translateY(5px) rotate(-1deg); }
    }
    
    .grid-pattern {
        background-image: 
            linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
        background-size: 60px 60px;
    }
    
    .scroll-reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease;
    }
    
    .scroll-reveal.revealed {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<!-- Hero Section with Modern Design -->
<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 grid-pattern opacity-30"></div>
    <div class="absolute top-20 left-10 w-72 h-72 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-gradient-to-r from-indigo-400/20 to-cyan-400/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-r from-emerald-400/20 to-blue-400/20 rounded-full blur-3xl"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Content -->
            <div class="text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-600/10 to-indigo-600/10 border border-blue-200/50 backdrop-blur-sm mb-8">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700">Platform Administrasi Digital Terdepan</span>
                </div>
                
                <!-- Main Title -->
                <h1 class="text-5xl lg:text-7xl font-bold mb-8 leading-tight">
                    <span class="text-gray-900">{{ $content->getContentValue('hero_title', 'Transformasi') }}</span>
                    <br>
                    <span class="gradient-text">Digital</span>
                    <br>
                    <span class="text-gray-700">Simalungun</span>
                </h1>
                
                <!-- Subtitle -->
                <p class="text-xl lg:text-2xl text-gray-600 mb-12 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    {{ $content->getContentValue('hero_subtitle', 'Revolusi layanan administrasi dengan teknologi AI dan cloud computing untuk pengalaman yang seamless dan efisien.') }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="relative flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            {{ $content->getContentValue('hero_cta_text', 'Mulai Sekarang') }}
                        </span>
                    </a>
                    <a href="#demo" class="group px-8 py-4 bg-white/80 backdrop-blur-sm text-gray-700 font-semibold rounded-2xl border border-gray-200/50 hover:bg-white hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a3 3 0 013-3h6a3 3 0 013 3v2M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $content->getContentValue('hero_secondary_cta', 'Lihat Demo') }}
                        </span>
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="flex flex-wrap items-center gap-8 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        100% Secure
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        24/7 Support
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        ISO Certified
                    </div>
                </div>
            </div>
            
            <!-- Interactive Dashboard Preview -->
            <div class="relative">
                <!-- Main Dashboard Card -->
                <div class="floating-card glassmorphism rounded-3xl p-8 shadow-2xl border border-white/20">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-lg">S</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-bold text-gray-800">SIMANTAP Dashboard</h3>
                                <p class="text-sm text-gray-500">Portal Administrasi</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-xs text-gray-600 font-medium">Online</span>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-2xl p-4 border border-emerald-200/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-600 text-sm font-medium">Dokumen Selesai</p>
                                    <p class="text-2xl font-bold text-emerald-700">1,247</p>
                                </div>
                                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-4 border border-blue-200/50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm font-medium">Proses Aktif</p>
                                    <p class="text-2xl font-bold text-blue-700">89</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activities -->
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-700 text-sm">Aktivitas Terbaru</h4>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-white/60 rounded-xl border border-gray-100/50">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
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
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
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
                
                <!-- Floating Elements -->
                <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-r from-purple-400 to-pink-400 rounded-2xl opacity-80 floating-card" style="animation-delay: -2s;"></div>
                <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-2xl opacity-80 floating-card" style="animation-delay: -4s;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
@if($content->getContentValue('features'))
<section class="py-24 bg-white scroll-reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 mb-6">
                <span class="text-sm font-medium text-blue-600">✨ Fitur Unggulan</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                Mengapa Memilih <span class="gradient-text">SIMANTAP</span>?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Platform administrasi digital yang menggabungkan teknologi AI, cloud computing, dan user experience terdepan untuk pelayanan publik yang revolusioner.
            </p>
        </div>
        
        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($content->getContentValue('features', []) as $index => $feature)
            <div class="group relative">
                <!-- Card -->
                <div class="h-full bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 hover:border-blue-200 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <!-- Icon -->
                    <div class="relative mb-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            @if(isset($feature['icon']))
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            @endif
                        </div>
                        <!-- Decorative Element -->
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-full opacity-70 group-hover:scale-125 transition-transform duration-300"></div>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors duration-300">
                        {{ $feature['title'] ?? 'Fitur' }}
                    </h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $feature['description'] ?? 'Deskripsi fitur' }}
                    </p>
                    
                    <!-- Learn More Link -->
                    <div class="flex items-center text-blue-600 font-medium group-hover:text-blue-700 transition-colors duration-300">
                        <span class="text-sm">Pelajari lebih lanjut</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Statistics Section -->
@if($content->getContentValue('statistics'))
<section class="py-24 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden scroll-reveal">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
        <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                Dipercaya Ribuan <span class="text-blue-400">Warga</span>
            </h2>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                Angka yang membuktikan komitmen kami dalam melayani masyarakat Simalungun
            </p>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($content->getContentValue('statistics', []) as $index => $stat)
            <div class="text-center group">
                <div class="glassmorphism rounded-3xl p-8 hover:bg-white/20 transition-all duration-300 hover:-translate-y-2">
                    <!-- Icon -->
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        @if($index === 0)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        @elseif($index === 1)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($index === 2)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    
                    <!-- Number -->
                    <div class="text-4xl lg:text-5xl font-bold text-white mb-2 group-hover:scale-110 transition-transform duration-300">
                        {{ $stat['value'] ?? '0' }}
                    </div>
                    
                    <!-- Label -->
                    <div class="text-blue-200 font-medium">
                        {{ $stat['label'] ?? 'Statistik' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-24 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 relative overflow-hidden scroll-reveal">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <!-- Floating Elements -->
    <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-20 right-20 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
    
    <div class="relative z-10 max-w-6xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <!-- Badge -->
        <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 mb-8">
            <svg class="w-4 h-4 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span class="text-sm font-medium text-white">Bergabung dengan 15,000+ pengguna aktif</span>
        </div>
        
        <!-- Main Title -->
        <h2 class="text-4xl lg:text-6xl font-bold text-white mb-8 leading-tight">
            Siap Memulai<br>
            <span class="text-blue-200">Transformasi Digital</span>?
        </h2>
        
        <!-- Subtitle -->
        <p class="text-xl lg:text-2xl text-blue-100 mb-12 max-w-3xl mx-auto leading-relaxed">
            Bergabunglah dengan ribuan warga Simalungun yang telah merasakan kemudahan layanan administrasi digital masa depan.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16">
            <a href="{{ route('register') }}" class="group relative px-10 py-5 bg-white text-blue-600 font-bold rounded-2xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span class="relative flex items-center justify-center text-lg">
                    <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    {{ $content->getContentValue('hero_cta_text', 'Daftar Gratis Sekarang') }}
                </span>
            </a>
            <a href="#demo" class="group px-10 py-5 border-2 border-white/30 text-white font-bold rounded-2xl backdrop-blur-sm hover:bg-white/10 hover:border-white/50 transform hover:-translate-y-2 transition-all duration-300">
                <span class="flex items-center justify-center text-lg">
                    <svg class="w-6 h-6 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a3 3 0 013-3h6a3 3 0 013 3v2M7 21h10a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Lihat Demo Interaktif
                </span>
            </a>
        </div>
        
        <!-- Trust Indicators -->
        <div class="flex flex-wrap justify-center items-center gap-12 text-white/80">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-medium">Gratis Selamanya</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-medium">Setup 2 Menit</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-400 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="font-medium">Dukungan Penuh</span>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-24 bg-gray-50 scroll-reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
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
        
        <!-- Testimonials Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <!-- Quote Icon -->
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                    </svg>
                </div>
                
                <!-- Content -->
                <p class="text-gray-600 mb-6 leading-relaxed">
                    "SIMANTAP benar-benar mengubah cara saya mengurus dokumen. Yang dulu butuh berhari-hari, sekarang bisa selesai dalam hitungan jam. Sangat membantu!"
                </p>
                
                <!-- Author -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">AB</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Ahmad Budiman</h4>
                        <p class="text-sm text-gray-500">Warga Pematang Siantar</p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <!-- Quote Icon -->
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                    </svg>
                </div>
                
                <!-- Content -->
                <p class="text-gray-600 mb-6 leading-relaxed">
                    "Interface yang mudah dipahami dan prosesnya transparan. Saya bisa tracking status permohonan real-time. Pelayanan yang sangat modern!"
                </p>
                
                <!-- Author -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">SR</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Sari Ramadhani</h4>
                        <p class="text-sm text-gray-500">Pengusaha UMKM</p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                <!-- Quote Icon -->
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-200 transition-colors duration-300">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                    </svg>
                </div>
                
                <!-- Content -->
                <p class="text-gray-600 mb-6 leading-relaxed">
                    "Customer service yang responsif dan sistem keamanan yang terjamin. Semua data pribadi terlindungi dengan baik. Sangat recommended!"
                </p>
                
                <!-- Author -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mr-4">
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
@endsection

@push('scripts')
<script>
    // Scroll Reveal Animation
    function revealOnScroll() {
        const reveals = document.querySelectorAll('.scroll-reveal');
        
        for (let i = 0; i < reveals.length; i++) {
            const windowHeight = window.innerHeight;
            const elementTop = reveals[i].getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add('revealed');
            }
        }
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize animations
    window.addEventListener('scroll', revealOnScroll);
    window.addEventListener('load', revealOnScroll);
    
    // Parallax effect for floating elements
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelectorAll('.floating-card');
        const speed = 0.5;
        
        parallax.forEach(element => {
            const yPos = -(scrolled * speed);
            element.style.transform = `translate3d(0, ${yPos}px, 0)`;
        });
    });
</script>
@endpush