{{-- resources/views/filament/petugas/modals/workload-statistics.blade.php --}}

<div class="space-y-6">
    {{-- Assignment Statistics Overview --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-blue-600 dark:text-blue-400" />
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
                    <x-heroicon-o-user-minus class="h-6 w-6 text-red-600 dark:text-red-400" />
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
                    <x-heroicon-o-user-check class="h-6 w-6 text-green-600 dark:text-green-400" />
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
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-orange-600 dark:text-orange-400" />
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
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            <x-heroicon-o-users class="h-5 w-5 inline mr-2" />
            Distribusi Workload per Petugas
        </h3>
        
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Petugas
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aktif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Selesai
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tingkat Penyelesaian
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($workloadDistribution as $petugas)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                {{ strtoupper(substr($petugas['petugas_name'], 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $petugas['petugas_name'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($petugas['active_count'] > 10) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($petugas['active_count'] > 5) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif
                                ">
                                    {{ $petugas['active_count'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $petugas['completed_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $petugas['total_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full 
                                            @if($petugas['completion_rate'] >= 80) bg-green-500
                                            @elseif($petugas['completion_rate'] >= 60) bg-yellow-500
                                            @else bg-red-500
                                            @endif
                                        " style="width: {{ $petugas['completion_rate'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ $petugas['completion_rate'] }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($petugas['active_count'] == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        Luang
                                    </span>
                                @elseif($petugas['active_count'] > 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <x-heroicon-o-fire class="w-3 h-3 mr-1" />
                                        Sangat Sibuk
                                    </span>
                                @elseif($petugas['active_count'] > 5)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                        Sibuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <x-heroicon-o-cog-6-tooth class="w-3 h-3 mr-1" />
                                        Normal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recommendations --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-light-bulb class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Rekomendasi
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <ul class="list-disc space-y-1 pl-5">
                        @php
                            $maxWorkload = collect($workloadDistribution)->max('active_count');
                            $minWorkload = collect($workloadDistribution)->min('active_count');
                            $unassignedCount = $assignmentStats['belum_ditugaskan'];
                            $overdueCount = $assignmentStats['overdue_assignment'];
                        @endphp
                        
                        @if($unassignedCount > 0)
                            <li>Ada {{ $unassignedCount }} permohonan yang belum ditugaskan. Pertimbangkan untuk melakukan auto-assignment.</li>
                        @endif
                        
                        @if($overdueCount > 0)
                            <li>{{ $overdueCount }} assignment sudah overdue. Segera lakukan follow-up atau pengalihan tugas.</li>
                        @endif
                        
                        @if($maxWorkload - $minWorkload > 5)
                            <li>Ada ketimpangan workload yang signifikan. Pertimbangkan untuk menyeimbangkan distribusi tugas.</li>
                        @endif
                        
                        @if($maxWorkload > 15)
                            <li>Beberapa petugas memiliki workload sangat tinggi (>15). Pertimbangkan untuk menambah kapasitas atau redistribusi.</li>
                        @endif
                        
                        @if(collect($workloadDistribution)->where('completion_rate', '<', 60)->count() > 0)
                            <li>Ada petugas dengan tingkat penyelesaian rendah (<60%). Mungkin perlu pelatihan atau dukungan tambahan.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>