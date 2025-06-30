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

    <div class="mb-8 -mx-4 -mt-4 sm:-mx-6 lg:-mx-8 rounded-t-xl overflow-hidden">
        <div class="h-40 w-full bg-gradient-to-r from-primary-600 to-primary-800"></div>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="-mt-28 sm:flex sm:items-end gap-6">
                <div class="relative group flex justify-center sm:justify-start">
                    <div class="flex h-32 w-32 items-center justify-center rounded-full bg-primary-200 text-5xl font-bold text-primary-700 ring-4 ring-white dark:ring-gray-800 shadow-lg">
                        {{ $getInitials($user->name) }}
                    </div>
                </div>
                <div class="mt-6 sm:flex-1 sm:min-w-0">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $user->email }}</p>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-center sm:justify-start gap-3">
                        @if ($user->verified_at)
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300">
                                <x-heroicon-s-check-badge class="h-5 w-5"/>
                                Warga Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-orange-100 dark:bg-orange-900/30 px-3 py-1.5 text-sm font-medium text-orange-700 dark:text-orange-300">
                                <x-heroicon-s-exclamation-triangle class="h-5 w-5"/>
                                Belum Diverifikasi
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                            <x-heroicon-s-identification class="h-5 w-5"/>
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
                <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600"/>
                Status Akun
            </h3>
            <div class="mt-4">
                @if ($user->verified_at)
                    <div class="flex items-start gap-3">
                        <div class="mt-1 w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                        <p class="text-gray-600 dark:text-gray-300 flex-1">
                            Akun Anda telah diverifikasi pada {{ $user->verified_at->format('d M Y') }}.
                        </p>
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
                    <div class="flex items-start gap-3">
                        <div class="mt-1 w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                        <div class="flex-1">
                            @if (count($missingFields) > 0)
                                <p class="text-gray-800 dark:text-gray-200 font-semibold">Akun Anda belum lengkap!</p>
                                <p class="text-gray-600 dark:text-gray-300 mt-1">Untuk melanjutkan proses verifikasi, mohon lengkapi data berikut:</p>
                                <ul class="list-disc list-inside mt-2 text-sm text-orange-700 dark:text-orange-400 space-y-1">
                                    @foreach ($missingFields as $field)
                                        <li>{{ $field }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-600 dark:text-gray-300">
                                    Data Anda sudah lengkap dan sedang dalam proses peninjauan oleh petugas.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                <x-heroicon-o-clock class="h-5 w-5 text-primary-600"/>
                Aktivitas Terakhir
            </h3>
            <div class="mt-4 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary-500 mt-2"></div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-300">Login terakhir: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Alamat IP: {{ $user->last_login_ip ?? 'Tidak diketahui' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <form wire:submit.prevent="updateProfile">
                {{ $this->form }}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                     <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        <x-heroicon-s-check-circle class="h-5 w-5"/>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>