<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Pastikan model User di-import

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function create()
    {
        // Pastikan view yang dipanggil sudah benar
        return view('auth.login');
    }

    /**
     * Menangani proses otentikasi.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba untuk melakukan otentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            
            // 3. Panggil metode yang benar untuk mendapatkan URL dan redirect
            return redirect()->to($user->getDashboardUrl());
        }

        // 4. Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Kombinasi email dan password yang Anda masukkan tidak cocok.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses logout.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}