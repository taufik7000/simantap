@props(['record'])

<div class="fi-header-heading mb-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($record->isAssigned())
                        <div class="flex items-center justify-center w-12 h-12 bg-primary-100 dark:bg-primary-500/20 rounded-lg">
                            {{-- PERBAIKAN DI SINI --}}
                        </div>
                    @else
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{-- PERBAIKAN DI SINI --}}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Status Penugasan
                    </h3>
                    
                    @if($record->isAssigned())
                        <div class="mt-1 space-y-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Ditugaskan kepada:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 ml-2">
                                    {{ $record->assignedTo->name }}
                                </span>
                            </p>
                            
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span>
                                    {{-- PERBAIKAN DI SINI --}}
                                    @svg('heroicon-s-clock', 'w-4 h-4 inline mr-1')
                                    {{ $record->assigned_at->format('d M Y, H:i') }}
                                </span>
                                
                                @if($record->assignedBy)
                                    <span>
                                        {{-- PERBAIKAN DI SINI --}}
                                        @svg('heroicon-s-user', 'w-4 h-4 inline mr-1')
                                        oleh {{ $record->assignedBy->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Permohonan ini belum ditugaskan kepada petugas manapun.
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-2">
                @if($record->isAssigned())
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-arrow-right-circle"
                        size="sm"
                        wire:click="mountAction('alihkan_tugas')"
                    >
                        Alihkan
                    </x-filament::button>
                @endif
            </div>
        </div>

        @if($record->isAssigned() && $record->isAssignmentOverdue(72))
            <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 rounded-lg">
                <div class="flex items-center">
                    {{-- PERBAIKAN DI SINI --}}
                    @svg('heroicon-s-exclamation-triangle', 'w-5 h-5 text-red-500 dark:text-red-400 mr-3')
                    <div class="text-sm">
                        <p class="text-red-800 dark:text-red-200 font-medium">Penugasan Overdue</p>
                        <p class="text-red-600 dark:text-red-300 mt-1">
                            Permohonan ini sudah ditugaskan lebih dari 72 jam dan memerlukan perhatian segera.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>