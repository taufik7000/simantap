<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman untuk memasukkan NIK.
     */
    public function showNikRequestForm()
    {
        return view('auth.passwords.nik');
    }

    /**
     * Memvalidasi NIK, membuat OTP & token, lalu mengirim OTP.
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['nik' => 'required|digits:16']);

        $user = User::where('nik', $request->nik)->first();

        if (!$user) {
            return back()->withErrors(['nik' => 'NIK tidak terdaftar di sistem kami.']);
        }
        
        $otp_code = rand(100000, 999999);
        $reset_token = Str::random(60);

        $user->forceFill([
            'whatsapp_verification_code' => $otp_code,
            'whatsapp_code_expires_at' => now()->addMinutes(10),
            'password_reset_token' => $reset_token,
            'password_reset_token_expires_at' => now()->addMinutes(60),
        ])->save();

        $settings = WhatsAppSetting::first();
        if ($settings && $user->nomor_whatsapp) {
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
                Log::error('Forgot Password OTP Exception: ' . $e->getMessage());
                return back()->withErrors(['nik' => 'Gagal mengirim kode verifikasi saat ini.']);
            }
        }
        
        // Arahkan ke halaman verifikasi OTP dengan token
        return redirect()->route('password.verify_otp_form', ['token' => $reset_token])
            ->with('success', 'Kami telah mengirimkan kode verifikasi ke nomor WhatsApp Anda.');
    }

    /**
     * Menampilkan halaman untuk memasukkan kode OTP.
     */
    public function showOtpForm(Request $request, $token)
    {
        // Cek apakah token masih ada dan valid
        $user = User::where('password_reset_token', $token)
            ->where('password_reset_token_expires_at', '>', now())->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['nik' => 'Sesi reset kata sandi tidak valid atau sudah kedaluwarsa.']);
        }

        return view('auth.passwords.verify-otp', ['token' => $token]);
    }

    /**
     * Memverifikasi kode OTP yang dimasukkan.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'code' => 'required|numeric|digits:6',
        ]);

        $user = User::where('password_reset_token', $request->token)->first();

        if (!$user || $user->whatsapp_verification_code !== $request->code || now()->isAfter($user->whatsapp_code_expires_at)) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.']);
        }

        // Jika OTP benar, arahkan ke halaman buat password baru
        return redirect()->route('password.reset_form', ['token' => $request->token]);
    }

    /**
     * Menampilkan halaman untuk membuat password baru.
     */
    public function showResetForm(Request $request, $token)
    {
        // Cek kembali token sebelum menampilkan form
        $user = User::where('password_reset_token', $token)
            ->where('password_reset_token_expires_at', '>', now())->first();
            
        if (!$user) {
            return redirect()->route('password.request')->withErrors(['nik' => 'Sesi reset kata sandi tidak valid atau sudah kedaluwarsa.']);
        }

        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Memproses dan menyimpan password baru.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('password_reset_token', $request->token)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['nik' => 'Sesi reset kata sandi tidak valid atau sudah kedaluwarsa.']);
        }
        
        // Update password dan hapus semua token
        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_token_expires_at' => null,
            'whatsapp_verification_code' => null,
            'whatsapp_code_expires_at' => null,
        ])->save();

        return redirect()->route('login')->with('success', 'Kata sandi Anda telah berhasil diubah! Silakan masuk.');
    }
}