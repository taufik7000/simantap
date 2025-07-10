@php
    $logs = $getRecord()->logs;
@endphp

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @foreach ($logs as $log)
            <li>
                <div class="relative pb-8">
                    @if (!$loop->last)
                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <div>
                            <span @class([
                                'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-900',
                                'bg-green-500' => in_array($log->status, ['disetujui', 'selesai']),
                                'bg-red-500' => in_array($log->status, ['ditolak', 'membutuhkan_revisi', 'butuh_perbaikan']),
                                'bg-blue-500' => !in_array($log->status, ['disetujui', 'selesai', 'ditolak', 'membutuhkan_revisi', 'butuh_perbaikan']),
                            ])>
                                @if(in_array($log->status, ['disetujui', 'selesai']))
                                    <x-heroicon-s-check class="h-5 w-5 text-white" />
                                @elseif(in_array($log->status, ['ditolak', 'membutuhkan_revisi', 'butuh_perbaikan']))
                                     <x-heroicon-s-x-mark class="h-5 w-5 text-white" />
                                @else
                                    <x-heroicon-s-arrow-path class="h-5 w-5 text-white" />
                                @endif
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $log->status->getLabel() }}
                                    @if($log->user)
                                      <span class="font-medium text-gray-900 dark:text-white">oleh {{ $log->user->name }}</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 italic">
                                    "{{ $log->catatan }}"
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $log->created_at->toIso8601String() }}">
                                    {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                </time>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>