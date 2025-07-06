<div>
    @php
        // Mengambil state/data dari komponen, hasilnya adalah objek: {'nama' => 'taufik', ...}
        $formData = $getState();
    @endphp

    <div class="px-3 py-2 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-200 dark:border-white/10">
        <dl class="divide-y divide-gray-200 dark:divide-white/10">
            {{-- Lakukan perulangan pada objek sebagai $key => $value --}}
            @forelse ((array) $formData as $key => $value)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 py-3">
                    <dt class="font-medium text-gray-900 dark:text-white">
                        {{-- Tampilkan 'nama_lengkap' menjadi 'Nama Lengkap' --}}
                        {{ str_replace('_', ' ', Str::title($key)) }}
                    </dt>
                    <dd class="text-gray-700 dark:text-gray-200 sm:col-span-2">
                        {{ $value ?? '-' }}
                    </dd>
                </div>
            @empty
                <div class="py-3 text-center text-gray-500">
                    <p>Warga tidak mengisi data formulir tambahan.</p>
                </div>
            @endforelse
        </dl>
    </div>
</div>