@props([
    'ticket',
    'showActions' => true,
    'layout' => 'responsive' // 'mobile', 'desktop', 'responsive'
])

@php
    $statusConfig = [
        'open' => [
            'label' => 'Terbuka', 
            'color' => 'bg-blue-100 text-blue-800 border-blue-200', 
            'icon' => 'heroicon-s-exclamation-circle',
            'dot' => 'bg-blue-500'
        ],
        'in_progress' => [
            'label' => 'Diproses', 
            'color' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 
            'icon' => 'heroicon-s-clock',
            'dot' => 'bg-yellow-500'
        ],
        'resolved' => [
            'label' => 'Selesai', 
            'color' => 'bg-green-100 text-green-800 border-green-200', 
            'icon' => 'heroicon-s-check-circle',
            'dot' => 'bg-green-500'
        ],
        'closed' => [
            'label' => 'Ditutup', 
            'color' => 'bg-gray-100 text-gray-800 border-gray-200', 
            'icon' => 'heroicon-s-lock-closed',
            'dot' => 'bg-gray-500'
        ]
    ];

    $priorityConfig = [
        'low' => ['label' => 'Rendah', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50'],
        'medium' => ['label' => 'Sedang', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
        'high' => ['label' => 'Tinggi', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50'],
        'urgent' => ['label' => 'Mendesak', 'color' => 'text-red-600', 'bg' => 'bg-red-50']
    ];

    $status = $statusConfig[$ticket->status] ?? $statusConfig['open'];
    $priority = $priorityConfig[$ticket->priority] ?? $priorityConfig['medium'];
    $unreadCount = $ticket->getUnreadCountForUser(auth()->id());
    $messageCount = $ticket->messages()->count();
    $lastMessage = $ticket->last_message;
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-primary-300 transition-all duration-300 overflow-hidden group']) }}>
    
    {{-- Mobile Layout --}}
    @if($layout === 'mobile' || $layout === 'responsive')
        <div class="{{ $layout === 'responsive' ? 'lg:hidden' : '' }}">
            <div class="p-4 space-y-4">
                {{-- Header dengan Status --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded font-medium">
                                {{ $ticket->kode_tiket }}
                            </span>
                            
                            @if($unreadCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                    <div class="w-1.5 h-1.5 {{ $status['dot'] }} rounded-full mr-1"></div>
                                    {{ $unreadCount }} baru
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-sm font-semibold text-gray-900 leading-5 mb-1">
                            {{ Str::limit($ticket->subject, 50) }}
                        </h3>
                    </div>
                    
                    {{-- Status Badge --}}
                    <div class="flex-shrink-0 ml-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $status['color'] }}">
                            <x-dynamic-component :component="$status['icon']" class="w-3 h-3 mr-1" />
                            {{ $status['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Priority & Category --}}
                <div class="flex items-center space-x-3">
                    <div class="flex items-center px-2 py-1 rounded-lg {{ $priority['bg'] }}">
                        <x-heroicon-o-flag class="w-3 h-3 mr-1 {{ $priority['color'] }}" />
                        <span class="text-xs font-medium {{ $priority['color'] }}">{{ $priority['label'] }}</span>
                    </div>
                    
                    <div class="flex items-center text-xs text-gray-500">
                        <x-heroicon-o-tag class="w-3 h-3 mr-1" />
                        <span class="truncate">{{ Str::limit($ticket->category_display, 20) }}</span>
                    </div>
                </div>

                {{-- Description Preview --}}
                <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                    {{ Str::limit($ticket->description, 80) }}
                </p>

                {{-- Meta Info --}}
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <div class="flex items-center space-x-3">
                        @if($messageCount > 0)
                            <span class="flex items-center">
                                <x-heroicon-o-chat-bubble-left class="w-3 h-3 mr-1" />
                                {{ $messageCount }}
                            </span>
                        @endif
                        
                        @if($ticket->assignedTo)
                            <span class="flex items-center truncate">
                                <x-heroicon-o-user class="w-3 h-3 mr-1 flex-shrink-0" />
                                <span class="truncate">{{ Str::limit($ticket->assignedTo->name, 15) }}</span>
                            </span>
                        @endif
                    </div>
                    
                    <span class="flex items-center flex-shrink-0">
                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                        {{ $ticket->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Action Button --}}
                @if($showActions)
                    <div class="pt-3 border-t border-gray-100">
                        <a href="{{ route('filament.warga.resources.tickets.view', $ticket) }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 active:bg-primary-800 transition-all duration-200 group-hover:shadow-md">
                            <x-heroicon-o-arrow-right class="w-4 h-4 mr-2" />
                            Lihat Detail
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Desktop Layout --}}
    @if($layout === 'desktop' || $layout === 'responsive')
        <div class="{{ $layout === 'responsive' ? 'hidden lg:block' : '' }}">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0 mr-6">
                        {{-- Header dengan Badges --}}
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="text-sm font-mono text-gray-500 bg-gray-100 px-3 py-1 rounded font-medium">
                                {{ $ticket->kode_tiket }}
                            </span>
                            
                            @if($unreadCount > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                    {{ $unreadCount }} pesan baru
                                </span>
                            @endif

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $status['color'] }}">
                                <x-dynamic-component :component="$status['icon']" class="w-3 h-3 mr-1.5" />
                                {{ $status['label'] }}
                            </span>

                            <div class="flex items-center px-3 py-1 rounded-lg {{ $priority['bg'] }}">
                                <x-heroicon-o-flag class="w-3 h-3 mr-1.5 {{ $priority['color'] }}" />
                                <span class="text-xs font-medium {{ $priority['color'] }}">Prioritas {{ $priority['label'] }}</span>
                            </div>
                        </div>

                        {{-- Title & Description --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                            {{ $ticket->subject }}
                        </h3>
                        <p class="text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                            {{ $ticket->description }}
                        </p>

                        {{-- Meta Information --}}
                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                            <span class="flex items-center">
                                <x-heroicon-o-tag class="w-4 h-4 mr-2" />
                                {{ $ticket->category_display }}
                            </span>
                            
                            <span class="flex items-center">
                                <x-heroicon-o-calendar class="w-4 h-4 mr-2" />
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </span>
                            
                            @if($ticket->assignedTo)
                                <span class="flex items-center">
                                    <x-heroicon-o-user class="w-4 h-4 mr-2" />
                                    Ditangani oleh {{ $ticket->assignedTo->name }}
                                </span>
                            @endif
                            
                            @if($messageCount > 0)
                                <span class="flex items-center">
                                    <x-heroicon-o-chat-bubble-left class="w-4 h-4 mr-2" />
                                    {{ $messageCount }} pesan
                                    @if($lastMessage)
                                        <span class="ml-1 text-xs">({{ $lastMessage->created_at->diffForHumans() }})</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Column --}}
                    @if($showActions)
                        <div class="flex-shrink-0 flex flex-col items-end space-y-3">
                            <a href="{{ route('filament.warga.resources.tickets.view', $ticket) }}" 
                               class="inline-flex items-center px-6 py-3 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group-hover:scale-105">
                                <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                                Lihat Detail
                            </a>
                            
                            <div class="text-right text-xs text-gray-500 space-y-1">
                                @if($ticket->isActive())
                                    <div class="flex items-center justify-end">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                        <span>Aktif sejak {{ $ticket->created_at->diffForHumans() }}</span>
                                    </div>
                                @elseif($ticket->resolved_at)
                                    <div class="flex items-center justify-end text-green-600">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        <span>Selesai {{ $ticket->resolved_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                                
                                @if($ticket->resolution_time)
                                    <div class="text-gray-400">
                                        Waktu resolusi: {{ $ticket->resolution_time }} jam
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>