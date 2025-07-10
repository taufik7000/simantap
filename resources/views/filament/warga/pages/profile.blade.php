<x-filament-panels::page>
    @php
        $user = auth()->user();
        // Menggunakan method baru 'isProfileComplete()'
        $isProfileComplete = $user->isProfileComplete();
    @endphp

    <div class="space-y-4 md:space-y-6 lg:space-y-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="h-20 sm:h-24 md:h-32 w-full bg-gradient-to-r from-primary-500 to-primary-700"></div>
            <div class="p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:gap-4 md:gap-6">
                    <div class="relative flex h-16 w-16 sm:h-20 sm:w-20 md:h-24 md:w-24 lg:h-32 lg:w-32 -mt-12 sm:-mt-14 md:-mt-16 lg:-mt-20 flex-shrink-0 mx-auto sm:mx-0">
                        <div class="flex h-full w-full items-center justify-center rounded-full bg-primary-100 dark:bg-primary-200 text-2xl sm:text-3xl md:text-4xl font-bold text-primary-700 ring-2 sm:ring-4 ring-white dark:ring-gray-800 shadow-lg">
                            {{ $user->initials }}
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-0 flex-1 min-w-0 text-center sm:text-left">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-3 md:mb-4 flex items-center gap-2 md:gap-3">
                    <x-heroicon-o-shield-check class="h-5 w-5 md:h-6 md:w-6 text-primary-600"/>
                    Status Akun & Profil
                </h3>
                
                {{-- Logika baru untuk menampilkan status --}}
                @if ($user->verified_at && $isProfileComplete)
                    <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0"/>
                        <p class="text-sm text-green-700 dark:text-green-300">Akun Anda sudah terverifikasi dan data profil sudah lengkap.</p>
                    </div>
                @elseif ($user->verified_at && !$isProfileComplete)
                    <div class="flex items-start gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0"/>
                        <p class="text-sm text-amber-700 dark:text-amber-300">Akun Anda sudah terverifikasi, namun data profil Anda belum lengkap. Silakan lengkapi di bawah.</p>
                    </div>
                @else
                     <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <x-heroicon-s-x-circle class="h-5 w-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0"/>
                        <p class="text-sm text-red-700 dark:text-red-300">Akun Anda belum terverifikasi. Silakan periksa kembali proses pendaftaran Anda.</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-3 md:mb-4 flex items-center gap-2 md:gap-3">
                    <x-heroicon-o-clock class="h-5 w-5 md:h-6 md:w-6 text-primary-600"/>
                    Aktivitas Terakhir
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Login terakhir: <span class="font-medium">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}</span>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Edit Profil</h2>
            </div>
            <div class="p-4 md:p-6">
                <form wire:submit.prevent="updateProfile" class="space-y-6">
                    {{ $this->form }}
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>