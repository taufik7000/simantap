<x-layouts.app>
    <x-slot name="title">{{ $article->title }}</x-slot>

    <div class="p-8 bg-white rounded-lg shadow">
        <h1 class="text-3xl font-bold mb-2">{{ $article->title }}</h1>
        <p class="text-sm text-gray-500 mb-6">
            Oleh: {{ $article->user->name }} &bull; Terakhir diperbarui: {{ $article->updated_at->format('d F Y') }}
        </p>
        
        <div class="prose max-w-none">
            {{-- Gunakan {!! !!} karena konten dari RichEditor adalah HTML --}}
            {!! $article->content !!}
        </div>

        <div class="mt-8 pt-4 border-t">
            <a href="{{ route('kb.index') }}" class="text-emerald-600 hover:underline">&larr; Kembali ke semua artikel</a>
        </div>
    </div>
</x-layouts.app>