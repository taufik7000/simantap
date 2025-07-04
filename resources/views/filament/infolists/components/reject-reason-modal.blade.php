{{-- resources/views/filament/infolists/components/reject-reason-modal.blade.php --}}

{{-- Modal untuk alasan penolakan revisi --}}
@if(session('reject_revision_id'))
    <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: block;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Tolak Revisi ke-{{ session('reject_revision_number') }}
                    </h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
                
                <!-- Modal Body -->
                <form action="{{ route('petugas.quick-revision-reject') }}" method="POST">
                    @csrf
                    <input type="hidden" name="revision_id" value="{{ session('reject_revision_id') }}">
                    
                    <div class="mb-4">
                        <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="reject_reason" 
                            id="reject_reason" 
                            rows="4" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Jelaskan mengapa revisi ini ditolak. Contoh: Dokumen KTP tidak jelas, format file tidak sesuai, dll."></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Berikan penjelasan yang jelas agar warga dapat memperbaiki revisi dengan tepat.
                        </p>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeRejectModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <x-heroicon-o-x-mark class="w-4 h-4 inline mr-1" />
                            Tolak Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Auto focus pada textarea saat modal terbuka
document.addEventListener('DOMContentLoaded', function() {
    @if(session('reject_revision_id'))
        const textarea = document.getElementById('reject_reason');
        if (textarea) {
            setTimeout(() => textarea.focus(), 100);
        }
    @endif
});

// Close modal ketika klik outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('reject-modal');
    if (modal && event.target === modal) {
        closeRejectModal();
    }
});

// Close modal dengan ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectModal();
    }
});
</script>