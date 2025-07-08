<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppVerificationController extends Controller
{
    /**
     * Menampilkan halaman form untuk memasukkan kode OTP.
     */
    public function show(Request $request)
    {
        // Pastikan pengguna datang dari halaman registrasi
        if (!$request->session()->has('user_id_for_verification')) {
            return redirect()->route('register');
        }
        
        return view('auth.whatsapp-verify');
    }

    /**
     * Memproses dan memvalidasi kode OTP yang dimasukkan.
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|numeric|digits:6']);

        $userId = $request->session()->get('user_id_for_verification');

        if (!$userId) {
            return redirect()->route('register')->withErrors('Sesi verifikasi tidak valid. Silakan daftar ulang.');
        }

        $user = User::find($userId);

        // Cek jika kode salah atau pengguna tidak ditemukan atau kode sudah kedaluwarsa
        if (!$user || $user->whatsapp_verification_code !== $request->code || now()->isAfter($user->whatsapp_code_expires_at)) {
            return back()->withErrors('Kode verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        // Jika berhasil, update data pengguna
        $user->forceFill([
            'whatsapp_verified_at' => now(),
            'whatsapp_verification_code' => null, // Hapus kode setelah digunakan
            'whatsapp_code_expires_at' => null,
        ])->save();
        
        // Hapus session dan loginkan pengguna
        $request->session()->forget('user_id_for_verification');
        Auth::login($user);

        return redirect($user->getDashboardUrl())->with('success', 'Verifikasi berhasil! Selamat datang.');
    }
}