<x-filament-panels::page>
    @php
        $user = auth()->user();
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

    <div class="space-y-4 md:space-y-6 lg:space-y-8">
        <!-- Profile Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- Header Gradient -->
            <div class="h-20 sm:h-24 md:h-32 w-full bg-gradient-to-r from-primary-500 to-primary-700"></div>
            
            <!-- Profile Content -->
            <div class="p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:gap-4 md:gap-6">
                    <!-- Avatar -->
                    <div class="relative flex h-16 w-16 sm:h-20 sm:w-20 md:h-24 md:w-24 lg:h-32 lg:w-32 -mt-12 sm:-mt-14 md:-mt-16 lg:-mt-20 flex-shrink-0 mx-auto sm:mx-0">
                        <div class="flex h-full w-full items-center justify-center rounded-full bg-primary-100 dark:bg-primary-200 text-2xl sm:text-3xl md:text-4xl font-bold text-primary-700 ring-2 sm:ring-4 ring-white dark:ring-gray-800 shadow-lg">
                            {{ $getInitials($user->name) }}
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="mt-2 sm:mt-0 flex-1 min-w-0 text-center sm:text-left">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                        
                        <!-- Status Badges -->
                        <div class="mt-3 flex flex-wrap gap-2 justify-center sm:justify-start">
                            @if ($user->verified_at)
                                <span class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 dark:bg-green-900/30 px-2.5 py-1 text-xs font-medium text-green-700 dark:text-green-300">
                                    <x-heroicon-s-check-badge class="h-3 w-3 sm:h-4 sm:w-4"/> 
                                    <span class="hidden sm:inline">Warga Terverifikasi</span>
                                    <span class="sm:hidden">Terverifikasi</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-x-1.5 rounded-md bg-amber-100 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300">
                                    <x-heroicon-s-exclamation-triangle class="h-3 w-3 sm:h-4 sm:w-4"/> 
                                    <span class="hidden sm:inline">Belum Diverifikasi</span>
                                    <span class="sm:hidden">Belum Verifikasi</span>
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-x-1.5 rounded-md bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                                <x-heroicon-s-identification class="h-3 w-3 sm:h-4 sm:w-4"/> 
                                <span class="hidden sm:inline">NIK: {{ $user->nik ?? 'Belum diisi' }}</span>
                                <span class="sm:hidden">{{ $user->nik ? 'NIK: ' . substr($user->nik, 0, 8) . '...' : 'NIK: -' }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Activity Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Account Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-3 md:mb-4 flex items-center gap-2 md:gap-3">
                    <x-heroicon-o-shield-check class="h-5 w-5 md:h-6 md:w-6 text-primary-600"/>
                    Status Akun
                </h3>
                
                @php
                    $completeness = $user->getProfileCompletenessStatus();
                    $status = $completeness['status'];
                    $missingFields = $completeness['missing'];
                @endphp
                
                @if ($status === 'Terverifikasi')
                    <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0"/>
                        <p class="text-sm text-green-700 dark:text-green-300">Akun Anda telah terverifikasi dan memiliki akses penuh.</p>
                    </div>
                @elseif ($status === 'Data Lengkap')
                    <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-s-clock class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"/>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Data Anda sudah lengkap dan sedang dalam proses peninjauan oleh petugas.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0"/>
                            <p class="text-sm text-amber-700 dark:text-amber-300">Lengkapi data berikut untuk melanjutkan proses verifikasi:</p>
                        </div>
                        <ul class="space-y-1.5 ml-2">
                            @foreach ($missingFields as $field)
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="h-1.5 w-1.5 rounded-full bg-amber-400 flex-shrink-0"></div>
                                    {{ $field }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            
            <!-- Last Activity Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg p-4 md:p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-3 md:mb-4 flex items-center gap-2 md:gap-3">
                    <x-heroicon-o-clock class="h-5 w-5 md:h-6 md:w-6 text-primary-600"/>
                    Aktivitas Terakhir
                </h3>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-arrow-right-on-rectangle class="h-4 w-4 text-gray-400"/>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Login: <span class="font-medium">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-globe-alt class="h-4 w-4 text-gray-400"/>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            IP: <span class="font-mono">{{ $user->last_login_ip ?? 'Tidak diketahui' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl shadow-sm md:shadow-lg border border-gray-200 dark:border-gray-700">
            <!-- Form Header -->
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-pencil-square class="h-6 w-6 text-primary-600 mt-0.5 flex-shrink-0"/>
                    <div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Edit Profil</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pastikan data yang Anda masukkan sudah benar dan sesuai dengan dokumen resmi.</p>
                    </div>
                </div>
            </div>
            
            <!-- Form Content -->
            <div class="p-4 md:p-6">
                <form wire:submit.prevent="updateProfile" class="space-y-6">
                    {{ $this->form }}
                    
                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <x-heroicon-s-x-mark class="h-4 w-4"/>
                            Batal
                        </button>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <x-heroicon-s-check-circle class="h-4 w-4"/>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>