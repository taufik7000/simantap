<x-layouts.app>
    {{-- Mengatur judul halaman di browser --}}
    <x-slot name="title">{{ $article->title }}</x-slot>

    <div class="bg-gray-50 py-12 sm:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('kb.index') }}" class="inline-flex items-center text-gray-500 hover:text-emerald-600">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Pusat Bantuan
                        </a>
                    </li>
                    @if ($article->category)
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <a href="{{ route('kb.index', ['kategori' => $article->category->slug]) }}" class="ms-1 text-gray-500 hover:text-emerald-600 md:ms-2">{{ $article->category->name }}</a>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                            <span class="ms-1 text-gray-700 font-medium md:ms-2">{{ Str::limit($article->title, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-8 sm:p-12">
                    <header class="mb-8">
                        @if ($article->category)
                            <p class="text-base font-semibold text-emerald-600 mb-2">{{ $article->category->name }}</p>
                        @endif
                        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">{{ $article->title }}</h1>
                        <p class="mt-4 text-sm text-gray-500">
                            Oleh <span class="font-medium text-gray-700">{{ $article->user->name }}</span>
                            &bull; Terakhir diperbarui pada {{ $article->updated_at->format('d F Y') }}
                        </p>
                    </header>
                    
                    <hr class="my-8 border-gray-200">
                    
                    <div class="prose prose-lg max-w-none prose-emerald">
                        {{-- Gunakan {!! !!} karena konten dari RichEditor adalah HTML --}}
                        {!! $article->content !!}
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <a href="{{ route('kb.index') }}" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-medium transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Kembali ke semua artikel
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>