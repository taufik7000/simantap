<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // 1. Validasi kredensial login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Lakukan otentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // ===============================================
            // AWAL BLOK PEMERIKSAAN VERIFIKASI WHATSAPP
            // ===============================================

            // 3. Cek jika user adalah 'warga' dan belum terverifikasi
            if ($user->hasRole('warga') && is_null($user->whatsapp_verified_at)) {
                
                // Ambil pengaturan WhatsApp
                $settings = WhatsAppSetting::first();
                
                // Hanya kirim ulang OTP jika fitur verifikasi diaktifkan oleh Kadis
                if ($settings && $settings->verification_enabled) {
                    
                    // Buat OTP baru
                    $otp_code = rand(100000, 999999);
                    $user->whatsapp_verification_code = $otp_code;
                    $user->whatsapp_code_expires_at = now()->addMinutes(10);
                    $user->save();

                    // Kirim ulang OTP
                    try {
                        Http::withToken($settings->access_token)->post(
                            'https://graph.facebook.com/v19.0/' . $settings->phone_number_id . '/messages',
                            [
                                'messaging_product' => 'whatsapp',
                                'to' => $user->nomor_whatsapp,
                                'type' => 'template',
                                'template' => [
                                    'name' => $settings->otp_template_name,
                                    'language' => ['code' => 'id'],
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

                    // Logout pengguna agar tidak masuk ke dasbor
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // Simpan ID pengguna di session dan arahkan ke halaman verifikasi
                    $request->session()->put('user_id_for_verification', $user->id);
                    return redirect()->route('whatsapp.verification.notice')
                        ->with('success', 'Akun Anda belum terverifikasi. Kami telah mengirimkan ulang kode OTP ke WhatsApp Anda.');
                }
            }

            // ===============================================
            // AKHIR BLOK PEMERIKSAAN
            // ===============================================

            // 4. Jika bukan warga yang belum terverifikasi, lanjutkan login seperti biasa
            $user->update([
                 'last_login_at' => Carbon::now(),
                 'last_login_ip' => $request->ip(),
            ]);
            return redirect()->to($user->getDashboardUrl());
        }

        // 5. Jika otentikasi gagal
        return back()->withErrors([
            'email' => 'Kombinasi email dan password tidak cocok.',
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