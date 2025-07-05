<x-layouts.app>
    <x-slot name="title">Pusat Bantuan</x-slot>

    <div class="space-y-4">
        @forelse ($articles as $article)
            <a href="{{ route('kb.show', $article->slug) }}" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold text-gray-900">{{ $article->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Terakhir diperbarui: {{ $article->updated_at->diffForHumans() }}
                </p>
            </a>
        @empty
            <div class="p-6 bg-white rounded-lg shadow text-center">
                <p>Belum ada artikel bantuan yang dipublikasikan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $articles->links() }}
    </div>
</x-layouts.app>