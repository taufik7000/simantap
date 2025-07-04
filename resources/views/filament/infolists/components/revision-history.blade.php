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
                                       target="_blank">
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
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-document-plus class="w-12 h-12 mx-auto mb-3 text-gray-400" />
            <p>Belum ada revisi yang diajukan</p>
        </div>
    @endforelse
</div>