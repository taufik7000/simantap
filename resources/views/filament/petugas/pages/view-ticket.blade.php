<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-heroicon-o-ticket class="h-5 w-5 text-primary-600"/>
                    Informasi Tiket
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Tiket</label>
                        <p class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $record->kode_tiket }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Permohonan Terkait</label>
                        <p class="text-sm">{{ $record->permohonan->kode_permohonan }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'open' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'closed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                ];
                                $statusColor = $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span @class(['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', $statusColor])>
                                {{ \App\Models\Ticket::STATUS_OPTIONS[$record->status] ?? $record->status }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioritas</label>
                        <div class="mt-1">
                            @php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                    'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                ];
                                $priorityColor = $priorityColors[$record->priority] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span @class(['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', $priorityColor])>
                                {{ \App\Models\Ticket::PRIORITY_OPTIONS[$record->priority] ?? $record->priority }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Ditangani Oleh</label>
                        <p class="text-sm">{{ $record->assignedTo?->name ?? 'Belum ditugaskan' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat</label>
                        <p class="text-sm">{{ $record->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    @if($record->resolved_at)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Diselesaikan</label>
                        <p class="text-sm">{{ $record->resolved_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Deskripsi Masalah</h3>
                <div class="prose prose-sm max-w-none dark:prose-invert">
                    <h4 class="text-base font-medium">{{ $record->subject }}</h4>
                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $record->description }}</p>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col h-[600px]">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-primary-600"/>
                        Percakapan dengan Tim Support
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Total {{ $record->messages()->public()->count() }} pesan
                    </p>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                    @forelse($record->messages()->public()->get() as $message)
                        <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <div @class([
                                    'px-4 py-3 rounded-2xl',
                                    'bg-primary-600 text-white' => $message->user_id === auth()->id(),
                                    'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' => $message->user_id !== auth()->id(),
                                ])>
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                    
                                    @if($message->attachments)
                                        <div class="mt-2 space-y-1">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('private')->url($attachment) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 text-xs underline hover:no-underline">
                                                    <x-heroicon-s-paper-clip class="h-3 w-3"/>
                                                    {{ basename($attachment) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div @class([
                                    'text-xs text-gray-500 dark:text-gray-400 mt-1',
                                    'text-right' => $message->user_id === auth()->id(),
                                    'text-left' => $message->user_id !== auth()->id(),
                                ])>
                                    <span class="font-medium">
                                        {{ $message->user_id === auth()->id() ? 'Anda' : $message->user->name }}
                                    </span>
                                    • {{ $message->created_at->diffForHumans() }}
                                    @if($message->read_at)
                                        <x-heroicon-s-check-circle class="inline h-3 w-3 text-primary-500 ml-1"/>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="h-12 w-12 text-gray-400 mx-auto mb-3"/>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada percakapan. Kirim pesan pertama Anda!</p>
                        </div>
                    @endforelse
                </div>

                @if(!$record->isActive())
                    <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <x-heroicon-s-lock-closed class="h-4 w-4"/>
                            Tiket ini sudah {{ $record->status === 'resolved' ? 'diselesaikan' : 'ditutup' }}.
                            @if($record->resolved_at)
                                Diselesaikan pada {{ $record->resolved_at->format('d M Y, H:i') }}.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
    @endpush
</x-filament-panels::page>