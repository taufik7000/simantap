{{-- resources/views/filament/petugas/modals/workload-statistics.blade.php --}}

<div class="space-y-6">
    {{-- Assignment Statistics Overview --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    {{-- PERBAIKAN --}}
                    @svg('heroicon-o-clipboard-document-list', 'h-6 w-6 text-blue-600 dark:text-blue-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Total Permohonan</p>
                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                        {{ $assignmentStats['total_permohonan'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    {{-- PERBAIKAN --}}
                    @svg('heroicon-o-user-minus', 'h-6 w-6 text-red-600 dark:text-red-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-900 dark:text-red-100">Belum Ditugaskan</p>
                    <p class="text-lg font-semibold text-red-900 dark:text-red-100">
                        {{ $assignmentStats['belum_ditugaskan'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    {{-- PERBAIKAN --}}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-900 dark:text-green-100">Sudah Ditugaskan</p>
                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                        {{ $assignmentStats['sudah_ditugaskan'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    {{-- PERBAIKAN --}}
                    @svg('heroicon-o-exclamation-triangle', 'h-6 w-6 text-orange-600 dark:text-orange-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-orange-900 dark:text-orange-100">Overdue</p>
                    <p class="text-lg font-semibold text-orange-900 dark:text-orange-100">
                        {{ $assignmentStats['overdue_assignment'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Workload Distribution per Petugas --}}
    <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
            @svg('heroicon-o-users', 'h-5 w-5 inline mr-2')
            Distribusi Workload per Petugas
        </h3>
        
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                {{-- ... (Isi tabel tidak perlu diubah, tapi saya akan perbaiki ikon di dalamnya) ... --}}
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Petugas</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aktif</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($workloadDistribution as $petugas)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $petugas['petugas_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($petugas['active_count'] > 10) bg-red-100 text-red-800 @elseif($petugas['active_count'] > 5) bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif
                                ">
                                    {{ $petugas['active_count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($petugas['active_count'] == 0)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">
                                        @svg('heroicon-s-check-circle', 'w-4 h-4 mr-1') Luang
                                    </span>
                                @elseif($petugas['active_count'] > 10)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-red-100 text-red-800">
                                        @svg('heroicon-s-fire', 'w-4 h-4 mr-1') Sangat Sibuk
                                    </span>
                                @elseif($petugas['active_count'] > 5)
                                     <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800">
                                        @svg('heroicon-s-clock', 'w-4 h-4 mr-1') Sibuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800">
                                        @svg('heroicon-s-cog-6-tooth', 'w-4 h-4 mr-1') Normal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data petugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>