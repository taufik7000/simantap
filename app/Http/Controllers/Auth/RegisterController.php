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
        $validated = $request->validate([
            'nik' => ['required', 'string', 'digits:16', 'unique:users,nik'],
            'nomor_kk' => ['required', 'string', 'digits:16'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nomor_whatsapp' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Password::min(8)],
            // Validasi untuk file gambar
            'foto_ktp' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_kk' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_tanda_tangan' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_selfie_ktp' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Proses penyimpanan file
        $paths = [];
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Simpan file ke storage/app/public/dokumen_warga/{nik}_{field}.ext
                $paths[$field] = $request->file($field)->storeAs(
                    'dokumen_warga',
                    $validated['nik'] . '_' . $field . '.' . $request->file($field)->extension(),
                    'public'
                );
            }
        }

        $user = User::create([
            'nik' => $validated['nik'],
            'nomor_kk' => $validated['nomor_kk'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'nomor_whatsapp' => $validated['nomor_whatsapp'],
            'password' => Hash::make($validated['password']),
            // Simpan path file ke database
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