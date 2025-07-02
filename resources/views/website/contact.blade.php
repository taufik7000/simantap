@extends('website.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-600 to-primary-800 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
            {{ $content->getContentValue('intro_title', 'Hubungi Kami') }}
        </h1>
        <p class="text-xl text-primary-100 max-w-3xl mx-auto leading-relaxed">
            {{ $content->getContentValue('intro_description', 'Tim kami siap membantu Anda. Jangan ragu untuk menghubungi kami jika membutuhkan bantuan atau memiliki pertanyaan.') }}
        </p>
    </div>
</section>

<!-- Contact Information -->
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Details -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Informasi Kontak</h2>
                
                <div class="space-y-8">
                    <!-- Address -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Alamat Kantor</h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $content->getContentValue('office_address', 'Jl. Sisingamangaraja No. 1, Pematang Siantar, Kabupaten Simalungun, Sumatera Utara 21118') }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    @if($content->getContentValue('phone'))
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Telepon</h3>
                            <a href="tel:{{ $content->getContentValue('phone') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                                {{ $content->getContentValue('phone') }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Email -->
                    @if($content->getContentValue('email'))
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                            <a href="mailto:{{ $content->getContentValue('email') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                                {{ $content->getContentValue('email') }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <!-- WhatsApp -->
                    @if($content->getContentValue('whatsapp'))
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $content->getContentValue('whatsapp')) }}" target="_blank" class="text-green-600 hover:text-green-700 font-medium">
                                {{ $content->getContentValue('whatsapp') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Office Hours & Social Media -->
            <div>
                <!-- Office Hours -->
                @if($content->getContentValue('office_hours'))
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Jam Operasional</h2>
                    <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl p-8 border border-primary-100">
                        <div class="space-y-4">
                            @foreach($content->getContentValue('office_hours', []) as $hour)
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-primary-600 rounded-full mr-4"></div>
                                <span class="text-gray-700 font-medium">{{ $hour }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Social Media -->
                @if($content->getContentValue('social_media'))
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Ikuti Kami</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($content->getContentValue('social_media', []) as $social)
                        <a href="{{ $social['url'] ?? '#' }}" target="_blank" class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-primary-200 transition-colors">
                                @if($social['platform'] === 'Facebook')
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                @elseif($social['platform'] === 'Instagram')
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C23.004 5.367 17.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.291C3.85 14.81 3.017 13.055 3.017 11.987c0-1.068.833-2.823 2.109-3.709.875-.8 2.026-1.291 3.323-1.291 1.297 0 2.448.49 3.323 1.291 1.276.886 2.109 2.641 2.109 3.709 0 1.068-.833 2.823-2.109 3.709-.875.801-2.026 1.292-3.323 1.292zm7.138 0c-1.297 0-2.448-.49-3.323-1.291-1.276-.886-2.109-2.641-2.109-3.709 0-1.068.833-2.823 2.109-3.709.875-.8 2.026-1.291 3.323-1.291 1.297 0 2.448.49 3.323 1.291 1.276.886 2.109 2.641 2.109 3.709 0 1.068-.833 2.823-2.109 3.709-.875.801-2.026 1.292-3.323 1.292z"/>
                                    </svg>
                                @elseif($social['platform'] === 'Twitter')
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $social['platform'] ?? 'Platform' }}</h3>
                                <p class="text-sm text-gray-500">Ikuti kami</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Lokasi Kantor</h2>
            <p class="text-lg text-gray-600">Kunjungi kantor kami untuk layanan langsung</p>
        </div>
        
        <!-- Google Maps Embed -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="aspect-w-16 aspect-h-9">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3986.9951234567890!2d99.0681234567890!3d2.9681234567890!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMsKwNTgnMDUuMiJOIDk5wrAwNCcwNS4yIkU!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid" 
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full h-96">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white mb-6">Mulai Gunakan Layanan Kami</h2>
        <p class="text-lg text-primary-200 mb-8">
            Daftar sekarang dan nikmati kemudahan layanan administrasi digital
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="bg-white text-primary-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                Daftar Sekarang
            </a>
            <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary-700 transition-colors">
                Masuk ke Akun
            </a>
        </div>
    </div>
</section>
@endsection