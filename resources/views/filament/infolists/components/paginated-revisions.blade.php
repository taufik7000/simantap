@php
    $record = $getRecord();
    $perPage = 3; // Tampilkan 3 revisi per halaman
    $page = request()->get('revision_page', 1);
    
    // Get revisions dengan pagination
    $totalRevisions = $record->revisions()->count();
    $totalPages = ceil($totalRevisions / $perPage);
    $offset = ($page - 1) * $perPage;
    $revisions = $record->revisions()
        ->orderBy('created_at', 'desc')
        ->skip($offset)
        ->take($perPage)
        ->get();
@endphp

<div class="space-y-4" data-section="revisions">
    <!-- Revisions Container dengan max height -->
    <div class="max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        @forelse($revisions as $revision)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 {{ $revision->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : ($revision->status === 'accepted' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') }}">
                <!-- Header Revisi -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ $revision->revision_number }}
                        </span>
                        <div>
                            <h4 class="font-medium text-gray-900">Revisi ke-{{ $revision->revision_number }}</h4>
                            <p class="text-sm text-gray-500">{{ $revision->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $revision->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($revision->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        @if($revision->status === 'pending')
                            Menunggu Review
                        @elseif($revision->status === 'accepted')
                            Diterima
                        @else
                            Ditolak
                        @endif
                    </span>
                </div>

                <!-- Catatan Warga -->
                @if($revision->catatan_revisi)
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Catatan dari Warga:</h5>
                        <p class="text-sm text-gray-600 bg-white p-3 rounded border">
                            {{ \Illuminate\Support\Str::limit($revision->catatan_revisi, 150) }}
                            @if(strlen($revision->catatan_revisi) > 150)
                                <button onclick="toggleFullNote({{ $revision->id }})" 
                                        class="text-indigo-600 hover:text-indigo-500 ml-1">
                                    Lihat selengkapnya
                                </button>
                            @endif
                        </p>
                        
                        @if(strlen($revision->catatan_revisi) > 150)
                            <div id="full-note-{{ $revision->id }}" class="hidden mt-2">
                                <p class="text-sm text-gray-600 bg-white p-3 rounded border">
                                    {{ $revision->catatan_revisi }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Berkas Revisi -->
                @if($revision->berkas_revisi && is_array($revision->berkas_revisi))
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Berkas Revisi ({{ count($revision->berkas_revisi) }} file):</h5>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach(array_slice($revision->berkas_revisi, 0, 2) as $index => $berkas)
                                @if(!empty($berkas['path_dokumen']))
                                    <div class="flex items-center justify-between p-2 bg-white border rounded text-sm">
                                        <div class="flex items-center space-x-2">
                                            <x-heroicon-o-document class="w-4 h-4 text-gray-400" />
                                            <span class="truncate">{{ $berkas['nama_dokumen'] ?? 'Dokumen ' . ($index + 1) }}</span>
                                        </div>
                                        <a href="{{ route('secure.download.revision', ['revision_id' => $revision->id, 'path' => $berkas['path_dokumen']]) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                                            Unduh
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                            
                            @if(count($revision->berkas_revisi) > 2)
                                <div class="text-center">
                                    <button onclick="toggleAllFiles({{ $revision->id }})" 
                                            class="text-sm text-indigo-600 hover:text-indigo-500">
                                        Lihat {{ count($revision->berkas_revisi) - 2 }} file lainnya
                                    </button>
                                </div>
                                
                                <div id="all-files-{{ $revision->id }}" class="hidden space-y-2">
                                    @foreach(array_slice($revision->berkas_revisi, 2) as $index => $berkas)
                                        @if(!empty($berkas['path_dokumen']))
                                            <div class="flex items-center justify-between p-2 bg-white border rounded text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <x-heroicon-o-document class="w-4 h-4 text-gray-400" />
                                                    <span class="truncate">{{ $berkas['nama_dokumen'] ?? 'Dokumen ' . ($index + 3) }}</span>
                                                </div>
                                                <a href="{{ route('secure.download.revision', ['revision_id' => $revision->id, 'path' => $berkas['path_dokumen']]) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                                    <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                                                    Unduh
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Catatan Petugas -->
                @if($revision->catatan_petugas)
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Catatan Petugas:</h5>
                        <p class="text-sm {{ $revision->status === 'approved' ? 'text-green-600' : 'text-red-600' }} bg-white p-3 rounded border">
                            {{ $revision->catatan_petugas }}
                        </p>
                    </div>
                @endif

                <!-- Review Info -->
                @if($revision->reviewed_at)
                    <div class="mt-2 text-xs text-gray-500">
                        Direview pada {{ $revision->reviewed_at->format('d M Y H:i') }}
                        @if($revision->reviewedBy)
                            oleh {{ $revision->reviewedBy->name }}
                        @endif
                    </div>
                @endif

                <!-- Quick Actions untuk Revisi Pending -->
                @if($revision->status === 'pending')
                    <div class="mt-4 flex space-x-2">
                        <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit"
                                    onclick="return confirm('Terima revisi ke-{{ $revision->revision_number }}?')"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                Terima
                            </button>
                        </form>
                        
                        <form action="{{ route('petugas.quick-revision-action') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="revision_id" value="{{ $revision->id }}">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit"
                                    onclick="return confirm('Tolak revisi ke-{{ $revision->revision_number }}?')"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <x-heroicon-o-x-mark class="w-4 h-4 mr-1" />
                                Tolak
                            </button>
                        </form>
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
    
    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                @if($page > 1)
                    <button onclick="loadRevisionPage({{ $page - 1 }})" 
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Previous
                    </button>
                @endif
                
                @if($page < $totalPages)
                    <button onclick="loadRevisionPage({{ $page + 1 }})" 
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Next
                    </button>
                @endif
            </div>
            
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $offset + 1 }}</span>
                        sampai
                        <span class="font-medium">{{ min($offset + $perPage, $totalRevisions) }}</span>
                        dari
                        <span class="font-medium">{{ $totalRevisions }}</span>
                        revisi
                    </p>
                </div>
                
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        @if($page > 1)
                            <button onclick="loadRevisionPage({{ $page - 1 }})" 
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Previous</span>
                                <x-heroicon-o-chevron-left class="h-5 w-5" />
                            </button>
                        @endif
                        
                        @for($i = 1; $i <= $totalPages; $i++)
                            <button onclick="loadRevisionPage({{ $i }})" 
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold {{ $i == $page ? 'bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' }}">
                                {{ $i }}
                            </button>
                        @endfor
                        
                        @if($page < $totalPages)
                            <button onclick="loadRevisionPage({{ $page + 1 }})" 
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Next</span>
                                <x-heroicon-o-chevron-right class="h-5 w-5" />
                            </button>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function loadRevisionPage(page) {
    // Reload page with revision_page parameter
    const url = new URL(window.location);
    url.searchParams.set('revision_page', page);
    window.location.href = url.toString();
}

function toggleFullNote(revisionId) {
    const fullNote = document.getElementById(`full-note-${revisionId}`);
    if (fullNote) {
        fullNote.classList.toggle('hidden');
    }
}

function toggleAllFiles(revisionId) {
    const allFiles = document.getElementById(`all-files-${revisionId}`);
    if (allFiles) {
        allFiles.classList.toggle('hidden');
    }
}
</script>