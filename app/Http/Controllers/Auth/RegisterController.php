<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Menampilkan form registrasi.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        // 1. Sesuaikan validasi: 'name' dan 'email' wajib, lainnya opsional.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'alamat' => ['nullable', 'string'],
            'nik' => ['nullable', 'string', 'digits:16', 'unique:users,nik'],
            'nomor_kk' => ['nullable', 'string', 'digits:16'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:15'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_kk' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_tanda_tangan' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_selfie_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // 2. Proses penyimpanan file hanya jika file diunggah
        $paths = [];
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store('dokumen_warga', 'public');
            }
        }

        // 3. Buat pengguna baru dengan data yang benar
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'alamat' => $validated['alamat'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'nomor_kk' => $validated['nomor_kk'] ?? null,
            'nomor_whatsapp' => $validated['nomor_whatsapp'] ?? null,
            'foto_ktp' => $paths['foto_ktp'] ?? null,
            'foto_kk' => $paths['foto_kk'] ?? null,
            'foto_tanda_tangan' => $paths['foto_tanda_tangan'] ?? null,
            'foto_selfie_ktp' => $paths['foto_selfie_ktp'] ?? null,
        ]);

        $user->assignRole('warga');
        auth()->login($user);

        return redirect($user->getDashboardUrl());
    }
}