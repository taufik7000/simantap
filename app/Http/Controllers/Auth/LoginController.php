<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses otentikasi dengan pemeriksaan verifikasi WhatsApp.
     */
    public function store(Request $request)
    {
        // 1. Validasi input umum
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // 2. Tentukan tipe login (email atau nik)
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nik';

        // 3. Siapkan kredensial untuk otentikasi
        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password')
        ];

        // 4. Lakukan otentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 5. Pemeriksaan Peran (Role-based check)
            if ($loginType === 'nik' && !$user->hasRole('warga')) {
                Auth::logout();
                return back()->withErrors(['login' => 'Login dengan NIK hanya untuk warga.'])->onlyInput('login');
            }
            if ($loginType === 'email' && $user->hasRole('warga')) {
                Auth::logout();
                return back()->withErrors(['login' => 'Warga harus login menggunakan NIK.'])->onlyInput('login');
            }

            // =======================================================
            // AWAL BLOK PEMERIKSAAN & PENGIRIMAN ULANG OTP (YANG HILANG)
            // =======================================================
            if ($user->hasRole('warga') && is_null($user->whatsapp_verified_at)) {
                $settings = WhatsAppSetting::first();
                if ($settings && $settings->verification_enabled) {
                    // Buat OTP baru dan simpan
                    $otp_code = rand(100000, 999999);
                    $user->whatsapp_verification_code = $otp_code;
                    $user->whatsapp_code_expires_at = now()->addMinutes(10);
                    $user->save();

                    // Kirim ulang OTP
                    try {
                        Http::withToken($settings->access_token)->post(
                            'https://graph.facebook.com/v19.0/' . $settings->phone_number_id . '/messages',
                            [
                                'messaging_product' => 'whatsapp', 'to' => $user->nomor_whatsapp, 'type' => 'template',
                                'template' => [
                                    'name' => $settings->otp_template_name, 'language' => ['code' => 'id'],
                                    'components' => [
                                        ['type' => 'body', 'parameters' => [['type' => 'text', 'text' => (string) $otp_code]]],
                                        ['type' => 'button', 'sub_type' => 'url', 'index' => '0', 'parameters' => [['type' => 'text', 'text' => 'verify-code']]],
                                        ['type' => 'button', 'sub_type' => 'COPY_CODE', 'index' => '1', 'parameters' => [['type' => 'text', 'text' => (string) $otp_code]]]
                                    ]
                                ]
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error('WhatsApp Resend on Login Exception: ' . $e->getMessage());
                    }

                    // Logout pengguna dan arahkan ke halaman verifikasi
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $request->session()->put('user_id_for_verification', $user->id);
                    return redirect()->route('whatsapp.verification.notice')
                        ->with('success', 'Akun Anda belum terverifikasi. Kami telah mengirimkan ulang kode OTP ke WhatsApp Anda.');
                }
            }
            // =======================================================
            // AKHIR BLOK PEMERIKSAAN
            // =======================================================

            // Jika semua pemeriksaan berhasil, lanjutkan login
            $user->update([
                 'last_login_at' => Carbon::now(),
                 'last_login_ip' => $request->ip(),
            ]);
            return redirect()->to($user->getDashboardUrl());
        }

        // Jika otentikasi gagal
        return back()->withErrors(['login' => 'Kombinasi kredensial dan password tidak cocok.'])->onlyInput('login');
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