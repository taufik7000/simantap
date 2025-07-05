<x-layouts.app>
    <x-slot name="title">Pusat Bantuan</x-slot>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-32">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 sm:mb-6">
                    Pusat Bantuan
                </h1>
                <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-primary-100 mb-6 sm:mb-8 max-w-3xl mx-auto px-4">
                    Temukan jawaban atas pertanyaan Anda dengan mudah dan cepat
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto px-4">
                    <form action="{{ route('kb.index') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 sm:pl-6 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="search" name="search" id="search"
                                   class="block w-full pl-12 sm:pl-14 pr-4 sm:pr-6 py-4 sm:py-5 text-base sm:text-lg bg-white/95 backdrop-blur-sm border-0 rounded-xl sm:rounded-2xl shadow-2xl placeholder-gray-500 focus:ring-4 focus:ring-white/30 focus:bg-white transition-all duration-300"
                                   placeholder="Ketik kata kunci..."
                                   value="{{ request('search') }}">
                        </div>
                        @if(request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements - Hidden on mobile -->
        <div class="hidden sm:block absolute top-0 left-0 w-72 h-72 bg-primary-400/20 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="hidden sm:block absolute bottom-0 right-0 w-96 h-96 bg-primary-400/20 rounded-full translate-x-1/2 translate-y-1/2"></div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-50 py-8 sm:py-12 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-12">
                
                <!-- Mobile Categories Toggle -->
                <div class="lg:hidden mb-6">
                    <button id="categoriesToggle" class="w-full bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between text-left">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4l-3 3.5a1.5 1.5 0 001 2.6H5m14 1.5l-3-3.5a1.5 1.5 0 00-1-2.6H5"></path>
                            </svg>
                            <span class="font-medium text-gray-900">Pilih Kategori</span>
                        </div>
                        <svg id="categoriesChevron" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Sidebar Categories -->
                <aside class="lg:col-span-1">
                    <div id="categoriesPanel" class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 overflow-hidden lg:sticky lg:top-8 hidden lg:block">
                        <div class="p-4 sm:p-6 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                            <h3 class="text-base sm:text-lg font-bold text-primary-900 flex items-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4l-3 3.5a1.5 1.5 0 001 2.6H5m14 1.5l-3-3.5a1.5 1.5 0 00-1-2.6H5"></path>
                                </svg>
                                Kategori
                            </h3>
                        </div>
                        <div class="p-3 sm:p-4 space-y-2">
                            <a href="{{ route('kb.index', ['search' => request('search')]) }}" 
                               class="group flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl transition-all duration-200 {{ !$selectedCategory ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/25' : 'hover:bg-primary-50 text-gray-700' }}">
                                <span class="font-medium text-sm sm:text-base">Semua Artikel</span>
                                <svg class="w-4 h-4 transition-transform duration-200 {{ !$selectedCategory ? 'text-white' : 'text-primary-600 group-hover:translate-x-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            
                            @foreach ($categories as $category)
                                @if($category->knowledge_bases_count > 0)
                                    <a href="{{ route('kb.index', ['kategori' => $category->slug, 'search' => request('search')]) }}" 
                                       class="group flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg sm:rounded-xl transition-all duration-200 {{ $selectedCategory && $selectedCategory->id == $category->id ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/25' : 'hover:bg-primary-50 text-gray-700' }}">
                                        <span class="font-medium text-sm sm:text-base">{{ $category->name }}</span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-bold px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full {{ $selectedCategory && $selectedCategory->id == $category->id ? 'bg-white/20 text-white' : 'bg-primary-100 text-primary-700' }}">
                                                {{ $category->knowledge_bases_count }}
                                            </span>
                                            <svg class="w-4 h-4 transition-transform duration-200 {{ $selectedCategory && $selectedCategory->id == $category->id ? 'text-white' : 'text-primary-600 group-hover:translate-x-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </aside>

                <!-- Articles List -->
                <div class="lg:col-span-3">
                    <div class="mb-6 sm:mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                            @if(request('search'))
                                Hasil pencarian untuk "{{ request('search') }}"
                            @else
                                {{ $selectedCategory ? $selectedCategory->name : 'Semua Artikel' }}
                            @endif
                        </h2>
                        <div class="w-16 sm:w-20 h-1 bg-gradient-to-r from-primary-600 to-primary-400 rounded-full"></div>
                    </div>

                    <div class="space-y-4 sm:space-y-6">
                        @forelse ($articles as $article)
                            <a href="{{ route('kb.show', $article->slug) }}" class="group block">
                                <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 lg:p-8 hover:shadow-xl hover:shadow-primary-500/10 hover:border-primary-300 transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-primary-700 transition-colors duration-200 mb-2 sm:mb-3 pr-4">
                                                {{ $article->title }}
                                            </h3>
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0 text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    <span class="font-medium text-primary-600 truncate">{{ $article->category->name ?? 'Tanpa Kategori' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="truncate">Diperbarui {{ $article->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2 sm:ml-6">
                                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 rounded-lg sm:rounded-xl flex items-center justify-center group-hover:bg-primary-600 transition-colors duration-200">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary-600 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 p-8 sm:p-12 lg:p-16 text-center">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 mx-auto mb-4 sm:mb-6 bg-gray-100 rounded-xl sm:rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                                <p class="text-gray-500 text-base sm:text-lg">
                                    @if(request('search'))
                                        Coba gunakan kata kunci lain atau periksa ejaan Anda.
                                    @else
                                        Tidak ada artikel bantuan dalam kategori ini saat ini.
                                    @endif
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="mt-8 sm:mt-12 flex justify-center">
                            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-200 p-2">
                                {{ $articles->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>