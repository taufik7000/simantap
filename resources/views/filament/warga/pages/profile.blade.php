<x-filament-panels::page>
    @php
        $user = auth()->user();
        // Fungsi untuk mendapatkan inisial dari nama
        $getInitials = function ($name) {
            $words = explode(' ', $name);
            $initials = '';
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } elseif (count($words) == 1 && strlen($words[0]) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 2));
            } elseif(count($words) == 1) {
                $initials = strtoupper(substr($words[0], 0, 1));
            }
            return $initials;
        };
    @endphp

    {{-- Header Profil Kustom --}}
    <div class="mb-8 -mx-4 -mt-4 sm:-mx-6 lg:-mx-8 rounded-t-xl overflow-hidden">
        <div class="h-40 w-full bg-gradient-to-r from-primary-600 to-primary-800"></div>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="-mt-28 sm:flex sm:items-end gap-6">
                <div class="relative group flex justify-center sm:justify-start">
                    <div class="flex h-32 w-32 items-center justify-center rounded-full bg-primary-200 text-5xl font-bold text-primary-700 ring-4 ring-white dark:ring-gray-800 shadow-lg">
                        {{-- Menampilkan inisial dari nama pengguna --}}
                        {{ $getInitials($user->name) }}
                    </div>
                    <div class="absolute inset-0 bg-black/30 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        {{-- Ikon Kamera untuk upload foto profil di masa depan --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                
                <div class="mt-6 sm:flex-1 sm:min-w-0">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white truncate">
                            {{ $user->name }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                            {{ $user->email }}
                        </p>
                    </div>
                    
                    <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-3">
                        @if ($user->verified_at)
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Warga Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-orange-100 dark:bg-orange-900/30 px-3 py-1.5 text-sm font-medium text-orange-700 dark:text-orange-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.001-1.742 3.001H4.42c-1.532 0-2.492-1.667-1.742-3.001l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                Belum Diverifikasi
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            NIK: {{ $user->nik ?? 'Belum diisi' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                Status Verifikasi
            </h3>
            <div class="flex items-start gap-3 mt-4">
                @if ($user->verified_at)
                    <div class="mt-1 w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-gray-600 dark:text-gray-300 flex-1">
                        Akun Anda telah diverifikasi pada {{ $user->verified_at->format('d M Y') }}. 
                        Status verifikasi memberi Anda akses penuh ke semua fitur aplikasi.
                    </p>
                @else
                    <div class="mt-1 w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                    <p class="text-gray-600 dark:text-gray-300 flex-1">
                        Akun Anda belum diverifikasi. Silakan lengkapi data diri dan tunggu verifikasi dari admin untuk mendapatkan akses penuh.
                    </p>
                @endif
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Aktivitas Terakhir
            </h3>
            <div class="mt-4 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary-500 mt-2"></div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-300">Login terakhir: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum ada data' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">IP Address: {{ $user->last_login_ip ?? 'Tidak diketahui' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary-500 mt-2"></div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-300">Pembaruan profil: {{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit Profil
            </h2>
        </div>
        
        <div class="p-6">
            <form wire:submit.prevent="updateProfile">
                {{-- Form dengan Tab akan dirender di sini --}}
                {{ $this->form }}

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <x-filament::button type="submit" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        Simpan Perubahan
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>