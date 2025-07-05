<x-layouts.app>
    <x-slot name="title">Semua Layanan Publik</x-slot>

    <div class="bg-slate-50">
        {{-- Hero Section --}}
        <div class="bg-gradient-to-b from-white to-slate-50 pt-20 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-gray-900">
                    Semua <span class="gradient-text">Layanan Publik</span>
                </h1>
                <p class="mt-4 sm:mt-6 max-w-2xl mx-auto text-lg text-gray-600">
                    Temukan dan ajukan berbagai layanan administrasi kependudukan dan perizinan dengan mudah, cepat, dan transparan.
                </p>
            </div>
        </div>

        {{-- Daftar Kategori dan Layanan --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="space-y-16">
                @forelse ($kategoriLayanans as $kategori)
                    <section>
                        {{-- Judul Kategori --}}
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                {{-- Menampilkan ikon dari database --}}
                                <x-icon :name="$kategori->icon" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">{{ $kategori->name }}</h2>
                                <p class="text-gray-500">{{ $kategori->description }}</p>
                            </div>
                        </div>

                        {{-- Grid untuk Kartu Layanan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($kategori->layanans as $layanan)
                                <div class="group bg-white rounded-2xl shadow-md border border-gray-200/80 hover:shadow-xl hover:border-emerald-500 transition-all duration-300 flex flex-col">
                                    <div class="p-6 flex-grow">
                                        <div class="flex items-start gap-4">
                                            {{-- Menampilkan ikon layanan dari database --}}
                                            @if($layanan->icon)
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                                                    <x-icon :name="$layanan->icon" class="w-6 h-6 text-gray-600" />
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="text-xl font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">
                                                    {{ $layanan->name }}
                                                </h3>
                                                @if(isset($layanan->description[0]['deskripsi_syarat']))
                                                    <p class="mt-2 text-sm text-gray-600 line-clamp-3">
                                                        {!! strip_tags($layanan->description[0]['deskripsi_syarat']) !!}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-6 py-4 rounded-b-2xl">
                                        <a href="{{ route('filament.warga.resources.permohonans.create', ['layanan_id' => $layanan->id]) }}" 
                                           class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                            Ajukan Sekarang
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="text-center py-16">
                        <p class="text-gray-500">Saat ini belum ada layanan yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>