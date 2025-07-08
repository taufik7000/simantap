<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi; // <-- Import package baru

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'nomor_whatsapp' => ['required', 'string', 'max:15'],
        ]);

        $formattedPhoneNumber = $this->formatPhoneNumber($validated['nomor_whatsapp']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nomor_whatsapp' => $formattedPhoneNumber,
        ]);
        $user->assignRole('warga');

        $settings = WhatsAppSetting::first();

        if ($settings && $settings->verification_enabled) {
            $otp_code = rand(100000, 999999);
            $user->whatsapp_verification_code = $otp_code;
            $user->whatsapp_code_expires_at = now()->addMinutes(10);
            $user->save();

            try {

                $settings = WhatsAppSetting::first();
                if (!$settings) {
                    Log::error('Pengaturan WhatsApp tidak ditemukan di database.');
                    return back()->withErrors(['nomor_whatsapp' => 'Layanan WhatsApp belum dikonfigurasi oleh admin.']);
                }

                // 2. Inisialisasi API dengan kredensial dari database
                $whatsapp_cloud_api = new WhatsAppCloudApi([
                    'from_phone_number_id' => $settings->phone_number_id,
                    'access_token' => $settings->access_token,
                ]);

                // 2. Kirim pesan template
                $whatsapp_cloud_api->sendTemplate(
                    $user->nomor_whatsapp,
                    $settings->otp_template_name,
                    'id', // Kode bahasa 'id' untuk Bahasa Indonesia
                    [$otp_code] // Kirim kode OTP sebagai parameter
                );

                // ===============================================
                // AKHIR BLOK PENGIRIMAN PESAN YANG BARU
                // ===============================================

            } catch (\Exception $e) {
                Log::error('WhatsApp Exception via Package: ' . $e->getMessage());
                return back()->withErrors(['nomor_whatsapp' => 'Gagal mengirim kode verifikasi.']);
            }

            $request->session()->put('user_id_for_verification', $user->id);
            return redirect()->route('whatsapp.verification.notice')
                ->with('success', 'Pendaftaran berhasil! Silakan periksa WhatsApp Anda untuk kode verifikasi.');
        } else {
            Auth::login($user);
            return redirect($user->getDashboardUrl());
        }
    }

    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (substr($number, 0, 1) == '0') {
            return '62' . substr($number, 1);
        }
        if (substr($number, 0, 2) == '62') {
            return $number;
        }
        return '62' . $number;
    }
}