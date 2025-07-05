<footer class="bg-slate-800 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-semibold gradient-text">SIMANTAP</h3>
                <p class="mt-2 text-sm text-gray-400">
                    Layanan Administrasi Digital Terpadu Kabupaten Simalungun.
                </p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-200">Tautan Cepat</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="#home" class="text-gray-400 hover:text-emerald-400">Home</a></li>
                    <li><a href="#features" class="text-gray-400 hover:text-emerald-400">Fitur Unggulan</a></li>
                    <li><a href="{{ route('kb.index') }}" class="text-gray-400 hover:text-emerald-400">Pusat Bantuan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-200">Layanan</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="/login" class="text-gray-400 hover:text-emerald-400">Kependudukan</a></li>
                    <li><a href="/login" class="text-gray-400 hover:text-emerald-400">Perizinan</a></li>
                    <li><a href="/login" class="text-gray-400 hover:text-emerald-400">Lainnya</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-200">Kontak</h4>
                <p class="mt-4 text-sm text-gray-400">
                    Dinas Kependudukan dan Pencatatan Sipil Kabupaten Simalungun
                </p>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-slate-700 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} SIMANTAP. Didukung oleh Pemerintah Kabupaten Simalungun.</p>
        </div>
    </div>
</footer>