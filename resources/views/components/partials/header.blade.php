<header class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-gray-200/50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="/" class="text-2xl font-bold gradient-text">
                    SIMANTAP
                </a>
            </div>

            {{-- Menu Navigasi (Desktop) --}}
            <div class="hidden md:flex md:items-center md:space-x-8">
                <a href="#home" class="font-medium text-gray-600 hover:text-emerald-600 transition">Home</a>
                <a href="#features" class="font-medium text-gray-600 hover:text-emerald-600 transition">Fitur</a>
                <a href="{{ route('kb.index') }}" class="font-medium text-gray-600 hover:text-emerald-600 transition">Pusat Bantuan</a>
            </div>

            {{-- Tombol Aksi (Desktop) --}}
            <div class="hidden md:flex items-center space-x-4">
                <a href="/login" class="px-5 py-2 text-sm font-semibold text-emerald-600 rounded-lg hover:bg-emerald-50 transition">
                    Login
                </a>
                <a href="/register" class="px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-lg hover:shadow-lg transition">
                    Daftar
                </a>
            </div>

            {{-- Tombol Menu (Mobile) - Tambahkan JavaScript jika perlu --}}
            <div class="md:hidden">
                <button class="text-gray-600 hover:text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>
    </nav>
</header>