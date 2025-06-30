{{-- ... --}}
<form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Nama Lengkap --}}
        {{-- NIK --}}
        {{-- No. KK --}}

        {{-- Tambahkan Input Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- No. Whatsapp --}}
        {{-- Password --}}
        {{-- Konfirmasi Password --}}
    </div>
    {{-- ... (input file dan tombol) --}}
</form>
{{-- ... --}}