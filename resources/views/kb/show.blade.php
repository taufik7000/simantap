<x-layouts.app>
    {{-- Mengatur judul halaman di browser --}}
    <x-slot name="title">{{ $article->title }}</x-slot>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
            <!-- Breadcrumb -->
            <nav class="flex mb-6 sm:mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('kb.index') }}" class="inline-flex items-center text-primary-200 hover:text-white transition-colors">
                            <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Pusat Bantuan
                        </a>
                    </li>
                    @if ($article->category)
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-primary-300 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <a href="{{ route('kb.index', ['kategori' => $article->category->slug]) }}" class="text-primary-200 hover:text-white transition-colors">{{ $article->category->name }}</a>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-primary-300 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <span class="text-white font-medium truncate max-w-xs sm:max-w-none">{{ Str::limit($article->title, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Article Header -->
            <div class="text-center sm:text-left">
                @if ($article->category)
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white mb-4">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ $article->category->name }}
                    </div>
                @endif
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 leading-tight">{{ $article->title }}</h1>
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-2 sm:space-y-0 text-primary-200">
                    <div class="flex items-center justify-center sm:justify-start">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="font-medium">{{ $article->user->name }}</span>
                    </div>
                    <div class="flex items-center justify-center sm:justify-start">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Terakhir diperbarui {{ $article->updated_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements - Hidden on mobile -->
        <div class="hidden sm:block absolute top-0 left-0 w-48 h-48 bg-primary-400/20 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="hidden sm:block absolute bottom-0 right-0 w-64 h-64 bg-primary-400/20 rounded-full translate-x-1/2 translate-y-1/2"></div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-50 py-8 sm:py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12">
                
                <!-- Article Content -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 sm:p-8 lg:p-12">
                            <!-- Content -->
                            <div class="prose prose-base sm:prose-lg max-w-none prose-primary prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900 prose-a:text-primary-600 hover:prose-a:text-primary-700">
                                {{-- Gunakan {!! !!} karena konten dari RichEditor adalah HTML --}}
                                {!! $article->content !!}
                            </div>

                            <!-- Back Button -->
                            <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-gray-200">
                                <a href="{{ route('kb.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:text-primary-800 font-medium rounded-lg transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Kembali ke semua artikel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="space-y-6 sm:space-y-8">
                        
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden lg:sticky lg:top-8">
                            <div class="p-4 sm:p-6 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                                <h3 class="text-base sm:text-lg font-bold text-primary-900 flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Aksi Cepat
                                </h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-3">
                                <!-- Print Article -->
                                <button onclick="window.print()" class="w-full flex items-center px-3 py-2.5 text-left text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span class="font-medium">Print Artikel</span>
                                </button>

                                <!-- Share Article -->
                                <button onclick="shareArticle()" class="w-full flex items-center px-3 py-2.5 text-left text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                    </svg>
                                    <span class="font-medium">Bagikan Artikel</span>
                                </button>

                                @if ($article->category)
                                <!-- View Category -->
                                <a href="{{ route('kb.index', ['kategori' => $article->category->slug]) }}" class="w-full flex items-center px-3 py-2.5 text-left text-gray-700 hover:bg-primary-50 hover:text-primary-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span class="font-medium">Lihat Kategori</span>
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Related Articles -->
                        @if(isset($relatedArticles) && $relatedArticles->count() > 0)
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-4 sm:p-6 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                                <h3 class="text-base sm:text-lg font-bold text-primary-900 flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4l-3 3.5a1.5 1.5 0 001 2.6H5m14 1.5l-3-3.5a1.5 1.5 0 00-1-2.6H5"></path>
                                    </svg>
                                    Artikel Terkait
                                </h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-4">
                                @foreach($relatedArticles as $relatedArticle)
                                <a href="{{ route('kb.show', $relatedArticle->slug) }}" class="group block p-3 hover:bg-primary-50 rounded-lg transition-colors duration-200">
                                    <h4 class="font-medium text-gray-900 group-hover:text-primary-700 transition-colors duration-200 mb-1 text-sm sm:text-base line-clamp-2">
                                        {{ $relatedArticle->title }}
                                    </h4>
                                    @if($relatedArticle->category)
                                    <p class="text-xs text-primary-600 font-medium">{{ $relatedArticle->category->name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">{{ $relatedArticle->updated_at->diffForHumans() }}</p>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Help Section -->
                        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-base sm:text-lg">Butuh Bantuan?</h3>
                            </div>
                            <p class="text-primary-100 mb-4 text-sm sm:text-base">Tidak menemukan jawaban yang Anda cari? Hubungi tim support kami.</p>
                            <a href="#" class="inline-flex items-center px-3 py-2 bg-white text-primary-600 hover:bg-primary-50 font-medium rounded-lg transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Hubungi Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for interactions -->
    <script>
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $article->title }}',
                    text: 'Artikel bantuan: {{ $article->title }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Link artikel telah disalin ke clipboard!');
                });
            }
        }

        // Smooth scroll untuk anchor links di dalam artikel
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
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
        });
    </script>

    <!-- Print Styles -->
    <style>
        @media print {
            .bg-primary-600, .bg-primary-700, .bg-primary-800 {
                background: #059669 !important;
                color: white !important;
            }
            
            nav, .lg\:col-span-1, button {
                display: none !important;
            }
            
            .lg\:col-span-3 {
                grid-column: span 4 !important;
            }
            
            .shadow-sm, .shadow-lg, .shadow-xl {
                box-shadow: none !important;
            }
        }
    </style>
</x-layouts.app>