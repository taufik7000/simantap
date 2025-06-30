{{-- ... (head) --}}
<body>
    {{-- ... --}}
    <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
        @csrf
        
        {{-- ... (input nama, nik, kk, dll) --}}

        {{-- Foto KTP --}}
        <div class="mb-4">
            <label for="foto_ktp">Foto KTP</label>
            <input id="foto_ktp" type="file" name="foto_ktp" required class="mt-1 block w-full ...">
            @error('foto_ktp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Foto KK --}}
        <div class="mb-4">
            <label for="foto_kk">Foto Kartu Keluarga</label>
            <input id="foto_kk" type="file" name="foto_kk" required class="mt-1 block w-full ...">
            @error('foto_kk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Foto Tanda Tangan --}}
        <div class="mb-4">
            <label for="foto_tanda_tangan">Foto Tanda Tangan (di atas kertas putih)</label>
            <input id="foto_tanda_tangan" type="file" name="foto_tanda_tangan" required class="mt-1 block w-full ...">
            @error('foto_tanda_tangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Foto Selfie dengan KTP --}}
        <div class="mb-4">
            <label for="foto_selfie_ktp">Foto Diri (Selfie) dengan memegang KTP</label>
            <input id="foto_selfie_ktp" type="file" name="foto_selfie_ktp" required class="mt-1 block w-full ...">
            @error('foto_selfie_ktp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- ... (input password dan tombol submit) --}}
    </form>
    {{-- ... --}}
</body>