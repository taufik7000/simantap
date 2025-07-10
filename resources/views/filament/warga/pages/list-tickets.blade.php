{{-- resources/views/filament/warga/pages/list-tickets.blade.php --}}

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header dengan Quick Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Total Tiket --}}
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-4 border border-primary-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-600 text-sm font-medium">Total Tiket</p>
                        <p class="text-2xl font-bold text-primary-900">{{ auth()->user()->tickets()->count() }}</p>
                        <p class="text-xs text-primary-600 mt-1">Semua waktu</p>
                    </div>
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-ticket class="w-5 h-5 text-white" />
                    </div>
                </div>
            </div>

            {{-- Tiket Aktif --}}
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-600 text-sm font-medium">Aktif</p>
                        <p class="text-2xl font-bold text-amber-900">{{ auth()->user()->tickets()->whereIn('status', ['open', 'in_progress'])->count() }}</p>
                        <p class="text-xs text-amber-600 mt-1">Butuh tindakan</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-clock class="w-5 h-5 text-white" />
                    </div>
                </div>
            </div>

            {{-- Tiket Selesai --}}
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-600 text-sm font-medium">Selesai</p>
                        <p class="text-2xl font-bold text-emerald-900">{{ auth()->user()->tickets()->whereIn('status', ['resolved', 'closed'])->count() }}</p>
                        <p class="text-xs text-emerald-600 mt-1">Terselesaikan</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-white" />
                    </div>
                </div>
            </div>

            {{-- Pesan Belum Dibaca --}}
            <div class="bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl p-4 border border-violet-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-violet-600 text-sm font-medium">Pesan Baru</p>
                        <p class="text-2xl font-bold text-violet-900">{{ auth()->user()->getUnreadMessagesCount() }}</p>
                        <p class="text-xs text-violet-600 mt-1">Belum dibaca</p>
                    </div>
                    <div class="w-10 h-10 bg-violet-500 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-envelope class="w-5 h-5 text-white" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tabs dengan Improved Design --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex overflow-x-auto" aria-label="Filter Tabs">
                    <a href="{{ route('filament.warga.resources.tickets.index') }}" 
                       class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ !request('status') ? 'text-primary-600 border-primary-600 bg-white -mb-px' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="flex items-center">
                            <x-heroicon-o-inbox class="w-4 h-4 mr-2" />
                            Semua Tiket
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ !request('status') ? 'bg-primary-100 text-primary-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ auth()->user()->tickets()->count() }}
                            </span>
                        </span>
                    </a>
                    
                    <a href="{{ route('filament.warga.resources.tickets.index', ['status' => 'active']) }}" 
                       class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ request('status') == 'active' ? 'text-primary-600 border-primary-600 bg-white -mb-px' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="flex items-center">
                            <x-heroicon-o-clock class="w-4 h-4 mr-2" />
                            Aktif
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ request('status') == 'active' ? 'bg-primary-100 text-primary-800' : 'bg-amber-100 text-amber-600' }}">
                                {{ auth()->user()->tickets()->whereIn('status', ['open', 'in_progress'])->count() }}
                            </span>
                        </span>
                    </a>
                    
                    <a href="{{ route('filament.warga.resources.tickets.index', ['status' => 'resolved']) }}" 
                       class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-all duration-200 {{ request('status') == 'resolved' ? 'text-primary-600 border-primary-600 bg-white -mb-px' : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="flex items-center">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            Selesai
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ request('status') == 'resolved' ? 'bg-primary-100 text-primary-800' : 'bg-emerald-100 text-emerald-600' }}">
                                {{ auth()->user()->tickets()->whereIn('status', ['resolved', 'closed'])->count() }}
                            </span>
                        </span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Search dan Sort untuk Mobile --}}
        <div class="lg:hidden space-y-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <input type="search" 
                       placeholder="Cari tiket berdasarkan judul..." 
                       class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
            </div>
            
            <div class="flex items-center justify-between">
                <select class="block px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option>Urutkan: Terbaru</option>
                    <option>Urutkan: Terlama</option>
                    <option>Urutkan: Prioritas</option>
                    <option>Urutkan: Status</option>
                </select>
                
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors duration-200">
                    <x-heroicon-o-funnel class="w-4 h-4 mr-1" />
                    Filter
                </button>
            </div>
        </div>

        {{-- Tickets List dengan Custom Component --}}
        <div class="space-y-4">
            @php
                $query = auth()->user()->tickets()->with(['layanan', 'permohonan', 'messages', 'assignedTo']);
                
                if (request('status') == 'active') {
                    $query->whereIn('status', ['open', 'in_progress']);
                } elseif (request('status') == 'resolved') {
                    $query->whereIn('status', ['resolved', 'closed']);
                }
                
                $tickets = $query->orderBy('created_at', 'desc')->get();
            @endphp

            @forelse($tickets as $ticket)
                {{-- Menggunakan komponen ticket-card yang telah dibuat --}}
                <x-ticket-card :ticket="$ticket" layout="responsive" class="ticket-card" />
            @empty
                {{-- Enhanced Empty State --}}
                <div class="bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300 p-12 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                        <x-heroicon-o-ticket class="w-10 h-10 text-gray-400" />
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                        @if(request('status') == 'active')
                            Tidak ada tiket aktif
                        @elseif(request('status') == 'resolved')
                            Tidak ada tiket yang selesai
                        @else
                            Belum ada tiket bantuan
                        @endif
                    </h3>
                    
                    <p class="text-gray-600 mb-8 max-w-md mx-auto leading-relaxed">
                        @if(request('status') == 'active')
                            Semua tiket Anda dalam kondisi baik. Tidak ada yang memerlukan perhatian khusus saat ini.
                        @elseif(request('status') == 'resolved')
                            Belum ada tiket yang diselesaikan. Tiket yang sudah selesai akan muncul di sini.
                        @else
                            Anda belum pernah membuat tiket bantuan. Buat tiket pertama Anda untuk mendapatkan bantuan dari tim support kami.
                        @endif
                    </p>
                    
                    @if(!request('status') || request('status') == 'active')
                        <div class="space-y-4">
                            <a href="{{ route('filament.warga.resources.tickets.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                Buat Tiket Baru
                            </a>
                            
                            <div class="text-sm text-gray-500">
                                <span>Atau lihat </span>
                                <a href="{{ route('kb.index') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                                    Pusat Bantuan
                                </a>
                                <span> untuk solusi mandiri</span>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('filament.warga.resources.tickets.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            <x-heroicon-o-arrow-left class="w-5 h-5 mr-2" />
                            Lihat Semua Tiket
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination (jika diperlukan untuk dataset besar) --}}
        @if($tickets->count() > 10)
            <div class="flex items-center justify-center pt-8">
                <nav class="flex items-center space-x-2">
                    <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                        <x-heroicon-o-chevron-left class="w-4 h-4" />
                    </button>
                    <button class="px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-primary-600 rounded-lg">
                        1
                    </button>
                    <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                        2
                    </button>
                    <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                        <x-heroicon-o-chevron-right class="w-4 h-4" />
                    </button>
                </nav>
            </div>
        @endif

        {{-- Quick Actions untuk Mobile --}}
        <div class="lg:hidden fixed bottom-6 right-6 z-50">
            <div class="flex flex-col items-end space-y-3">
                {{-- Tombol Filter/Search --}}
                <button class="inline-flex items-center justify-center w-12 h-12 bg-white text-gray-600 rounded-full shadow-lg border border-gray-200 hover:bg-gray-50 transition-all duration-200">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                </button>
                
                {{-- Tombol Buat Tiket Baru --}}
                <a href="{{ route('filament.warga.resources.tickets.create') }}" 
                   class="inline-flex items-center justify-center w-14 h-14 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-700 hover:shadow-xl hover:scale-105 transition-all duration-200">
                    <x-heroicon-o-plus class="w-6 h-6" />
                </a>
            </div>
        </div>
    </div>

    {{-- Custom Styles untuk Animations dan Responsive Design --}}
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ticket-card {
            animation: fadeInUp 0.3s ease-out;
        }
        
        .ticket-card:nth-child(2) { animation-delay: 0.1s; }
        .ticket-card:nth-child(3) { animation-delay: 0.2s; }
        .ticket-card:nth-child(4) { animation-delay: 0.3s; }
        
        /* Custom scrollbar untuk mobile tabs */
        .overflow-x-auto::-webkit-scrollbar {
            height: 2px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 1px;
        }
        
        /* Smooth transitions */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        
        /* Improved hover effects */
        .hover-lift:hover {
            transform: translateY(-2px);
        }
        
        /* Focus styles untuk accessibility */
        .focus\:ring-2:focus {
            box-shadow: 0 0 0 2px rgba(var(--primary-500), 0.5);
        }
    </style>

    {{-- JavaScript untuk Enhanced Interactivity --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh untuk unread messages count
            setInterval(function() {
                // Update unread count setiap 30 detik
                fetch('/api/unread-messages-count')
                    .then(response => response.json())
                    .then(data => {
                        const unreadElements = document.querySelectorAll('[data-unread-count]');
                        unreadElements.forEach(el => {
                            el.textContent = data.count;
                            if (data.count > 0) {
                                el.classList.add('animate-pulse');
                            } else {
                                el.classList.remove('animate-pulse');
                            }
                        });
                    });
            }, 30000);

            // Search functionality
            const searchInput = document.querySelector('input[type="search"]');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase();
                        const ticketCards = document.querySelectorAll('.ticket-card');
                        
                        ticketCards.forEach(card => {
                            const title = card.querySelector('h3').textContent.toLowerCase();
                            const description = card.querySelector('p').textContent.toLowerCase();
                            
                            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                                card.style.display = 'block';
                                card.style.animation = 'fadeInUp 0.3s ease-out';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    }, 300);
                });
            }

            // Smooth scrolling untuk anchor links
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

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + K untuk focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[type="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
                
                // Escape untuk clear search
                if (e.key === 'Escape') {
                    const searchInput = document.querySelector('input[type="search"]');
                    if (searchInput && searchInput === document.activeElement) {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.blur();
                    }
                }
            });

            // Loading states untuk links
            document.querySelectorAll('a[href*="tickets"]').forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.href.includes('#')) {
                        const loadingSpinner = document.createElement('div');
                        loadingSpinner.className = 'inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin ml-2';
                        this.appendChild(loadingSpinner);
                        
                        // Disable link temporarily
                        this.style.pointerEvents = 'none';
                        this.style.opacity = '0.7';
                    }
                });
            });

            // Intersection Observer untuk lazy loading animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '50px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all ticket cards
            document.querySelectorAll('.ticket-card').forEach(card => {
                observer.observe(card);
            });

            // Auto-update timestamps
            function updateTimestamps() {
                document.querySelectorAll('[data-timestamp]').forEach(el => {
                    const timestamp = el.getAttribute('data-timestamp');
                    const date = new Date(timestamp);
                    el.textContent = timeAgo(date);
                });
            }

            function timeAgo(date) {
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                const intervals = [
                    { label: 'tahun', seconds: 31536000 },
                    { label: 'bulan', seconds: 2592000 },
                    { label: 'hari', seconds: 86400 },
                    { label: 'jam', seconds: 3600 },
                    { label: 'menit', seconds: 60 }
                ];
                
                for (const interval of intervals) {
                    const count = Math.floor(seconds / interval.seconds);
                    if (count > 0) {
                        return `${count} ${interval.label} yang lalu`;
                    }
                }
                
                return 'Baru saja';
            }

            // Update timestamps every minute
            updateTimestamps();
            setInterval(updateTimestamps, 60000);

            // Mobile menu toggle functionality
            const filterToggle = document.querySelector('[data-filter-toggle]');
            const filterMenu = document.querySelector('[data-filter-menu]');
            
            if (filterToggle && filterMenu) {
                filterToggle.addEventListener('click', function() {
                    filterMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!filterToggle.contains(e.target) && !filterMenu.contains(e.target)) {
                        filterMenu.classList.add('hidden');
                    }
                });
            }

            // Toast notifications untuk feedback
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
                
                const bgColors = {
                    success: 'bg-emerald-500 text-white',
                    error: 'bg-red-500 text-white',
                    warning: 'bg-amber-500 text-black',
                    info: 'bg-primary-500 text-white'
                };
                
                toast.className += ` ${bgColors[type]}`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                // Show toast
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);
                
                // Hide toast after 3 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }

            // Handle form submissions with loading states
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        `;
                    }
                });
            });
        });

        // PWA-like features
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => console.log('SW registered'))
                .catch(error => console.log('SW registration failed'));
        }

        // Add to homescreen prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button jika diperlukan
            const installButton = document.querySelector('#install-app');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        deferredPrompt = null;
                        installButton.style.display = 'none';
                    });
                });
            }
        });
    </script>

    {{-- Additional CSS untuk custom animations --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Loading shimmer effect */
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Custom focus styles */
        .focus-visible:focus-visible {
            outline: 2px solid rgb(var(--primary-500));
            outline-offset: 2px;
        }
        
        /* Improved mobile touch targets */
        @media (max-width: 768px) {
            button, a {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Dark mode support (jika diperlukan) */
        @media (prefers-color-scheme: dark) {
            .dark-mode-support {
                background-color: #1f2937;
                color: #f9fafb;
            }
        }
    </style>
</x-filament-panels::page>