<div class="space-y-4">
    @forelse($getRecord()->revisions()->orderBy('created_at', 'desc')->get() as $revision)
        <div class="border border-gray-200 rounded-lg p-4 {{ $revision->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : ($revision->status === 'accepted' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') }}">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ $revision->revision_number }}
                    </span>
                    <div>
                        <h4 class="font-medium text-gray-900">Revisi ke-{{ $revision->revision_number }}</h4>
                        <p class="text-sm text-gray-500">{{ $revision->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        @if($revision->status === 'pending')
                            Menunggu Review
                        @elseif($revision->status === 'accepted')
                            Diterima
                        @else
                            Ditolak
                        @endif
                    </span>

                    <!-- Action Buttons - Hanya tampil untuk status pending dan user yang memiliki akses -->
                    @if($revision->status === 'pending' && auth()->user()->hasRole(['petugas', 'admin', 'kadis']))
                        <div class="flex space-x-1">
                            <!-- Tombol Terima -->
                            <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                        onclick="return confirm('Apakah Anda yakin ingin menerima revisi ke-{{ $revision->revision_number }}?')"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors border border-green-200"
                                        title="Terima Revisi">
                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                    Terima
                                </button>
                            </form>

                            <!-- Tombol Tolak -->
                            <button type="button"
                                    onclick="showRejectModal('{{ $revision->id }}', '{{ $revision->revision_number }}')"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 transition-colors border border-red-200"
                                    title="Tolak Revisi">
                                <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                Tolak
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if($revision->catatan_revisi)
                <div class="mb-3">
                    <h5 class="text-sm font-medium text-gray-700 mb-1">Catatan dari Warga:</h5>
                    <p class="text-sm text-gray-600 bg-white p-3 rounded border">{{ $revision->catatan_revisi }}</p>
                </div>
            @endif

            @if($revision->berkas_revisi && is_array($revision->berkas_revisi))
                <div class="mb-3">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Berkas Revisi:</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($revision->berkas_revisi as $index => $berkas)
                            @if(!empty($berkas['path_dokumen']))
                                <div class="flex items-center justify-between p-2 bg-white border rounded">
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-document class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm text-gray-700 truncate">{{ $berkas['nama_dokumen'] ?? 'Dokumen ' . ($index + 1) }}</span>
                                    </div>
                                    <a href="{{ route('secure.download.revision', ['revision_id' => $revision->id, 'path' => $berkas['path_dokumen']]) }}"
                                       class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                       target="_blank"
                                       title="Unduh {{ $berkas['nama_dokumen'] ?? 'Dokumen ' . ($index + 1) }}">
                                        <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                                        Unduh
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if($revision->catatan_petugas)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <h5 class="text-sm font-medium text-gray-700 mb-1">Catatan Petugas:</h5>
                    <p class="text-sm {{ $revision->status === 'accepted' ? 'text-green-600' : 'text-red-600' }} bg-white p-3 rounded border">{{ $revision->catatan_petugas }}</p>
                </div>
            @endif

            @if($revision->reviewed_at)
                <div class="mt-2 text-xs text-gray-500">
                    Direview pada {{ $revision->reviewed_at->format('d M Y H:i') }}
                    @if($revision->reviewedBy)
                        oleh {{ $revision->reviewedBy->name }}
                    @endif
                </div>
            @endif

            <!-- Quick Action Buttons for Pending Revisions (Alternative Layout) -->
            @if($revision->status === 'pending' && auth()->user()->hasRole(['petugas', 'admin', 'kadis']))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Aksi Quick Review:</span>
                        <div class="flex space-x-2">
                            <!-- Tombol Terima -->
                            <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                        onclick="return confirm('Terima revisi ke-{{ $revision->revision_number }}? \n\nRevisi akan diterima dan permohonan akan dilanjutkan ke proses berikutnya.')"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                                    <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                    Terima Revisi
                                </button>
                            </form>

                            <!-- Tombol Tolak -->
                            <button type="button"
                                    onclick="showRejectModal('{{ $revision->id }}', '{{ $revision->revision_number }}')"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm">
                                <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                                Tolak Revisi
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-document-plus class="w-12 h-12 mx-auto mb-3 text-gray-400" />
            <p>Belum ada revisi yang diajukan</p>
        </div>
    @endforelse
</div>

<!-- Modal untuk Tolak Revisi -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                    Tolak Revisi
                </h3>
                <button type="button" onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <form id="rejectForm" action="{{ route('petugas.quick-revision-action') }}" method="POST">
                @csrf
                <input type="hidden" name="revision_id" id="reject_revision_id">
                <input type="hidden" name="action" value="reject">
                
                <div class="mb-4">
                    <label for="catatan_petugas" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="catatan_petugas" 
                        id="catatan_petugas" 
                        rows="4" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
                        placeholder="Jelaskan mengapa revisi ini ditolak dan apa yang perlu diperbaiki..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRejectModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <x-heroicon-o-x-circle class="w-4 h-4 inline mr-1" />
                        Tolak Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(revisionId, revisionNumber) {
    document.getElementById('reject_revision_id').value = revisionId;
    document.getElementById('modal-title').textContent = `Tolak Revisi ke-${revisionNumber}`;
    document.getElementById('catatan_petugas').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
    
    // Focus on textarea
    setTimeout(() => {
        document.getElementById('catatan_petugas').focus();
    }, 100);
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('catatan_petugas').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('rejectModal').classList.contains('hidden')) {
        hideRejectModal();
    }
});
</script>