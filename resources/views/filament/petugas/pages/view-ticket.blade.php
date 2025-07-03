<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Tiket -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Tiket</h3>
                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ $record->kode_tiket }}</span>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemohon</label>
                        <p class="text-sm font-medium">{{ $record->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $record->user->email }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        @php
                            $statusColor = match($record->status) {
                                'open' => 'bg-blue-100 text-blue-800',
                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                'resolved' => 'bg-green-100 text-green-800',
                                'closed' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span @class(['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', $statusColor])>
                            {{ \App\Models\Ticket::STATUS_OPTIONS[$record->status] ?? $record->status }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioritas</label>
                        @php
                              $priorityColor = match($record->priority) {
                                'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'medium' => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-300',
                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-300',
                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            };
                        @endphp
                        <span @class(['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', $priorityColor])>
                            {{ \App\Models\Ticket::PRIORITY_OPTIONS[$record->priority] ?? $record->priority }}
                        </span>
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
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Deskripsi Masalah</h3>
                <div class="prose prose-sm max-w-none dark:prose-invert">
                    <h4 class="text-base font-medium">{{ $record->subject }}</h4>
                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $record->description }}</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
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
                            <div class="max-w-xs lg:max-w-md xl:max-w-lg">
                                <div @class([
                                    'rounded-lg px-4 py-2 text-sm',
                                    'bg-primary-600 text-white' => $message->user_id === auth()->id(),
                                    'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' => $message->user_id !== auth()->id(),
                                ])>
                                    <p class="whitespace-pre-wrap">{{ $message->message }}</p>
                                    
                                    @if($message->attachments)
                                        <div class="mt-2 space-y-1">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ Storage::disk('private')->url($attachment) }}" 
                                                   target="_blank" 
                                                   class="block text-xs {{ $message->user_id === auth()->id() ? 'text-primary-100 hover:text-white' : 'text-primary-600 hover:text-primary-800' }}">
                                                    📎 {{ basename($attachment) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
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
                            <p class="text-gray-500 dark:text-gray-400">Belum ada percakapan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input Area untuk Petugas -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                    <form wire:submit="sendMessage" class="space-y-3">
                        <div class="flex gap-3">
                            <!-- Text Input -->
                            <div class="flex-1">
                                <textarea 
                                    wire:model="messageText"
                                    placeholder="Tulis balasan untuk warga..."
                                    rows="2"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 resize-none"
                                    @keydown.enter.prevent="
                                        if (!$event.shiftKey) {
                                            $wire.sendMessage();
                                        }
                                    "
                                ></textarea>
                            </div>
                            
                            <!-- Send Button -->
                            <div class="flex flex-col justify-end">
                                <button 
                                    type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!$wire.messageText?.trim()"
                                >
                                    <x-heroicon-o-paper-airplane class="h-4 w-4"/>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Toggle untuk Internal Message dan File Upload -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model="isInternal"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                    >
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Pesan Internal</span>
                                </label>
                            </div>
                            
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    wire:model="attachments"
                                    multiple
                                    accept="image/*,application/pdf,.doc,.docx"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-600 dark:file:text-gray-300 dark:hover:file:bg-gray-500"
                                />
                            </div>
                            
                            @if($attachments)
                                <div class="text-xs text-gray-500">
                                    {{ count($attachments) }} file dipilih
                                </div>
                            @endif
                        </div>
                        
                        <!-- Hint -->
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Tekan Enter untuk kirim, Shift+Enter untuk baris baru</span>
                            <div class="flex items-center gap-4">
                                @if($isInternal)
                                    <span class="text-orange-600 dark:text-orange-400">⚠️ Pesan internal (tidak terlihat warga)</span>
                                @endif
                                <span>Max 5MB per file</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-scroll to bottom -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });

        // Auto-scroll setelah pesan baru dikirim
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                const messagesContainer = document.getElementById('messages-container');
                if (messagesContainer) {
                    setTimeout(() => {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }, 100);
                }
            });
        });
    </script>
</x-filament-panels::page>