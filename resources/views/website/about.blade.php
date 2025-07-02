@extends('website.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 to-primary-800 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
            {{ $content->getContentValue('intro_title', 'Tentang SIMANTAP') }}
        </h1>
        <p class="text-xl text-primary-100 max-w-3xl mx-auto leading-relaxed">
            {{ $content->getContentValue('intro_description', 'SIMANTAP adalah platform digital yang dikembangkan untuk mempermudah masyarakat Kabupaten Simalungun dalam mengakses berbagai layanan administrasi pemerintahan.') }}
        </p>
    </div>
</section>

<!-- Vision Section -->
@if($content->getContentValue('vision'))
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Visi Kami</h2>
        <div class="bg-primary-50 rounded-2xl p-8 border border-primary-100">
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! $content->getContentValue('vision') !!}
            </div>
        </div>
    </div>
</section>
@endif

<!-- Mission Section -->
@if($content->getContentValue('mission'))
<section class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Misi Kami</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Komitmen kami dalam melayani masyarakat Simalungun
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($content->getContentValue('mission', []) as $index => $missionItem)
            <div class="flex items-start group">
                <div class="flex-shrink-0 w-10 h-10 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold mr-4 group-hover:bg-primary-700 transition-colors">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <p class="text-gray-700 leading-relaxed">{{ $missionItem }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Values Section -->
@if($content->getContentValue('values'))
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nilai-Nilai Kami</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Prinsip yang menjadi landasan dalam setiap layanan yang kami berikan
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($content->getContentValue('values', []) as $value)
            <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-8 text-center border border-primary-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $value['title'] ?? 'Nilai' }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $value['description'] ?? 'Deskripsi nilai' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white mb-6">Bergabunglah dengan Kami</h2>
        <p class="text-lg text-primary-200 mb-8">
            Rasakan kemudahan layanan administrasi digital yang telah dipercaya ribuan warga Simalungun
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                Daftar Sekarang
            </a>
            <a href="{{ route('website.page', 'kontak') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary-700 transition-colors">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>
@endsection