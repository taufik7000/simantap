<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppVerificationController extends Controller
{
    /**
     * Menampilkan halaman form untuk memasukkan kode OTP.
     */
    public function show(Request $request)
    {
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
        // ... (method verify() Anda sudah benar dan tidak perlu diubah)
        $request->validate(['code' => 'required|numeric|digits:6']);

        $userId = $request->session()->get('user_id_for_verification');
        if (!$userId) {
            return redirect()->route('register')->withErrors('Sesi verifikasi tidak valid. Silakan daftar ulang.');
        }

        $user = User::find($userId);

        if (!$user || $user->whatsapp_verification_code !== $request->code || now()->isAfter($user->whatsapp_code_expires_at)) {
            return back()->withErrors('Kode verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        $user->forceFill([
            'whatsapp_verified_at' => now(),
            'verified_at' => now(),
            'whatsapp_verification_code' => null,
            'whatsapp_code_expires_at' => null,
        ])->save();
        
        $request->session()->forget('user_id_for_verification');
        Auth::login($user);

        return redirect($user->getDashboardUrl())->with('success', 'Verifikasi berhasil! Selamat datang.');
    }

    /**
     * Method baru untuk mengirim ulang kode OTP.
     */
    public function resend(Request $request)
    {
        $userId = $request->session()->get('user_id_for_verification');
        if (!$userId) {
            return redirect()->route('register')->withErrors('Sesi verifikasi tidak valid.');
        }

        $user = User::find($userId);
        $settings = WhatsAppSetting::first();

        if (!$user || !$settings) {
            return back()->withErrors('Gagal mengirim ulang kode. Konfigurasi tidak ditemukan.');
        }

        $otp_code = rand(100000, 999999);
        $user->whatsapp_verification_code = $otp_code;
        $user->whatsapp_code_expires_at = now()->addMinutes(10);
        $user->save();

        // ===============================================
        // AWAL BLOK PENGIRIMAN YANG DISAMAKAN DENGAN REGISTERCONTROLLER
        // ===============================================
        try {
            $response = Http::withToken($settings->access_token)->post(
                'https://graph.facebook.com/v19.0/' . $settings->phone_number_id . '/messages',
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $user->nomor_whatsapp,
                    'type' => 'template',
                    'template' => [
                        'name' => $settings->otp_template_name,
                        'language' => ['code' => 'id'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [['type' => 'text', 'text' => (string) $otp_code]]
                            ],
                            [
                                'type' => 'button',
                                'sub_type' => 'url',
                                'index' => '0',
                                'parameters' => [['type' => 'text', 'text' => 'verify-code']]
                            ],
                            [
                                'type' => 'button',
                                'sub_type' => 'COPY_CODE',
                                'index' => '1',
                                'parameters' => [['type' => 'text', 'text' => (string) $otp_code]]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->failed()) {
                Log::error('WhatsApp OTP (Resend) Gagal Terkirim:', $response->json());
                return back()->withErrors('Gagal mengirim ulang kode verifikasi.');
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Resend Exception: ' . $e->getMessage());
            return back()->withErrors('Gagal mengirim ulang kode verifikasi.');
        }
        // ===============================================
        // AKHIR BLOK PENGIRIMAN YANG DISAMAKAN
        // ===============================================
        
        return back()->with('success', 'Kode verifikasi baru telah berhasil dikirim ulang.');
    }
}