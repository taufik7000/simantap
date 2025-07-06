<nav class="bg-white dark:bg-gray-800 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Bagian Kiri: Logo & Nama Panel --}}
            <div class="flex items-center">
                <a href="{{ route('filament.warga.pages.dashboard') }}" class="flex-shrink-0 flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow">
                        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6h1.5m-1.5 3h1.5m-1.5 3h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-800 dark:text-white">Dasbor Warga</span>
                </a>
                
                {{-- Menu Utama (Desktop) --}}
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        @foreach (Filament\Facades\Filament::getNavigationItems() as $item)
                            <a href="{{ $item->getUrl() }}" 
                               class="{{ $item->isActive() ? 'bg-emerald-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} px-3 py-2 rounded-md text-sm font-medium">
                                {{ $item->getLabel() }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Profil & Logout --}}
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    {{-- Di sini Anda bisa menambahkan menu notifikasi atau profil --}}
                    <div>
                        {{ filament()->renderHook('panels::user-menu.before') }}
                        {{ filament()->getUserMenu() }}
                        {{ filament()->renderHook('panels::user-menu.after') }}
                    </div>
                </div>
            </div>

            {{-- Tombol Hamburger Menu untuk Mobile --}}
            <div class="-mr-2 flex md:hidden">
                <button type="button" x-on:click="open = ! open" class="inline-flex items-center justify-center rounded-md bg-gray-100 p-2 text-gray-500 hover:bg-gray-200">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>
        </div>
    </div>
</nav>