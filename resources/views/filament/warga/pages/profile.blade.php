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

    <div class="mb-8 -mx-4 -mt-4 sm:-mx-6 lg:-mx-8 rounded-xl overflow-hidden shadow-lg">
        <div class="relative h-40 md:h-48 w-full bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 right-0 px-4 sm:px-6 lg:px-8 pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="relative group">
                        <div class="flex h-24 w-24 sm:h-32 sm:w-32 items-center justify-center rounded-2xl bg-white/95 backdrop-blur-sm text-3xl sm:text-4xl font-bold text-primary-600 shadow-2xl ring-4 ring-white/50 transition-all duration-300 group-hover:scale-105">
                            {{ $getInitials($user->name) }}
                        </div>
                        <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-3xl font-bold text-white mb-1 drop-shadow-lg">
                            {{ $user->name }}
                        </h1>
                        <p class="text-primary-100 text-sm sm:text-base mb-3 drop-shadow">
                            {{ $user->email }}
                        </p>
                        
                        <div class="flex flex-wrap gap-2">
                            @if ($user->verified_at)
                                <span class="inline-flex items-center gap-x-1.5 rounded-full bg-emerald-500/90 backdrop-blur-sm px-3 py-1.5 text-xs sm:text-sm font-medium text-white shadow-lg">
                                    <x-heroicon-s-check-badge class="h-4 w-4"/>
                                    Warga Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-x-1.5 rounded-full bg-amber-500/90 backdrop-blur-sm px-3 py-1.5 text-xs sm:text-sm font-medium text-white shadow-lg">
                                    <x-heroicon-s-exclamation-triangle class="h-4 w-4"/>
                                    Belum Diverifikasi
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-white/20 backdrop-blur-sm px-3 py-1.5 text-xs sm:text-sm font-medium text-white shadow-lg">
                                <x-heroicon-s-identification class="h-4 w-4"/>
                                NIK: {{ $user->nik ?? 'Belum diisi' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-xl">
                        <x-heroicon-o-shield-check class="h-6 w-6 text-primary-600 dark:text-primary-400"/>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Status Akun
                    </h3>
                </div>
                
                <div class="space-y-4">
                    @if ($user->verified_at)
                        <div class="flex items-start gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                            <div class="mt-1">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shadow-lg shadow-emerald-500/50"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-emerald-800 dark:text-emerald-200 font-medium">
                                    Akun Terverifikasi
                                </p>
                                <p class="text-emerald-600 dark:text-emerald-300 text-sm mt-1">
                                    Diverifikasi pada {{ $user->verified_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    @else
                        @php
                            $missingFields = [];
                            if (empty($user->nik)) $missingFields[] = 'NIK';
                            if (empty($user->nomor_kk)) $missingFields[] = 'Nomor Kartu Keluarga';
                            if (empty($user->alamat)) $missingFields[] = 'Alamat Lengkap';
                            if (empty($user->foto_ktp)) $missingFields[] = 'Foto KTP';
                            if (empty($user->foto_kk)) $missingFields[] = 'Foto Kartu Keluarga';
                            if (empty($user->foto_tanda_tangan)) $missingFields[] = 'Foto Tanda Tangan';
                            if (empty($user->foto_selfie_ktp)) $missingFields[] = 'Foto Selfie dengan KTP';
                        @endphp
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    <div class="w-3 h-3 rounded-full bg-amber-500 animate-pulse shadow-lg shadow-amber-500/50"></div>
                                </div>
                                <div class="flex-1">
                                    @if (count($missingFields) > 0)
                                        <p class="text-amber-800 dark:text-amber-200 font-semibold mb-2">
                                            Akun Belum Lengkap
                                        </p>
                                        <p class="text-amber-700 dark:text-amber-300 text-sm mb-3">
                                            Lengkapi data berikut untuk verifikasi:
                                        </p>
                                        <div class="space-y-2">
                                            @foreach ($missingFields as $field)
                                                <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                                                    <x-heroicon-s-minus class="h-3 w-3"/>
                                                    {{ $field }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-amber-800 dark:text-amber-200 font-semibold">
                                            Sedang Ditinjau
                                        </p>
                                        <p class="text-amber-700 dark:text-amber-300 text-sm mt-1">
                                            Data lengkap dan sedang dalam proses peninjauan
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-xl">
                        <x-heroicon-o-clock class="h-6 w-6 text-primary-600 dark:text-primary-400"/>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Aktivitas Terakhir
                    </h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="mt-2">
                            <div class="w-2 h-2 rounded-full bg-primary-500 shadow-lg shadow-primary-500/50"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 dark:text-gray-100 font-medium">
                                Login Terakhir
                            </p>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-mono">
                                IP: {{ $user->last_login_ip ?? 'Tidak diketahui' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-xl">
                    <x-heroicon-o-user-circle class="h-6 w-6 text-primary-600 dark:text-primary-400"/>
                </div>
                Edit Profile
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Perbarui informasi profil dan data pribadi Anda
            </p>
        </div>
        
        <div class="p-6">
            <form wire:submit.prevent="updateProfile">
                {{ $this->form }}
                
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transform hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        <x-heroicon-s-check-circle class="h-5 w-5"/>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>