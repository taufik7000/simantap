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
     * Menyimpan pengguna baru dan menjalankan alur verifikasi WhatsApp jika aktif.
     */
    public function store(Request $request)
    {
        // 1. Validasi data input, termasuk nomor WhatsApp yang sekarang wajib diisi
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'alamat' => ['nullable', 'string'],
            'nik' => ['nullable', 'string', 'digits:16', 'unique:users,nik'],
            'nomor_kk' => ['nullable', 'string', 'digits:16'],
            'nomor_whatsapp' => ['required', 'string', 'max:15'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_kk' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_tanda_tangan' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_selfie_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // 2. Format nomor WhatsApp ke format internasional (62xxx)
        $formattedPhoneNumber = $this->formatPhoneNumber($validated['nomor_whatsapp']);

        // 3. Proses penyimpanan file
        $paths = [];
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $paths[$field] = $request->file($field)->store('dokumen_warga', 'public');
            }
        }

        // 4. Buat pengguna baru dengan semua data
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'alamat' => $validated['alamat'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'nomor_kk' => $validated['nomor_kk'] ?? null,
            'nomor_whatsapp' => $formattedPhoneNumber,
            'foto_ktp' => $paths['foto_ktp'] ?? null,
            'foto_kk' => $paths['foto_kk'] ?? null,
            'foto_tanda_tangan' => $paths['foto_tanda_tangan'] ?? null,
            'foto_selfie_ktp' => $paths['foto_selfie_ktp'] ?? null,
        ]);

        $user->assignRole('warga');

        // 5. Ambil pengaturan WhatsApp dari database
        $settings = WhatsAppSetting::first();

        // 6. Cek apakah verifikasi diaktifkan oleh Kadis
        if ($settings && $settings->verification_enabled) {
            $otp_code = rand(100000, 999999);
            $user->whatsapp_verification_code = $otp_code;
            $user->whatsapp_code_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                // ===============================================
                // AWAL BLOK PENGIRIMAN SESUAI POSTMAN
                // ===============================================
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
                                // Komponen untuk Body
                                [
                                    'type' => 'body',
                                    'parameters' => [
                                        ['type' => 'text', 'text' => (string) $otp_code]
                                    ]
                                ],
                                // Komponen untuk Tombol URL (index 0)
                                [
                                    'type' => 'button',
                                    'sub_type' => 'url',
                                    'index' => '0',
                                    'parameters' => [
                                        ['type' => 'text', 'text' => (string) $otp_code]
                                    ]
                                ],
                                // Komponen untuk Tombol Salin Kode (index 1)
                                [
                                    'type' => 'button',
                                    'sub_type' => 'COPY_CODE',
                                    'index' => '1',
                                    'parameters' => [
                                        ['type' => 'text', 'text' => (string) $otp_code]
                                    ]
                                ]
                            ]
                        ]
                    ]
                );
                // ===============================================
                // AKHIR BLOK PENGIRIMAN
                // ===============================================

                if ($response->failed()) {
                    Log::error('WhatsApp OTP Gagal Terkirim:', $response->json());
                    return back()->withErrors(['nomor_whatsapp' => 'Gagal mengirim kode verifikasi. Layanan sedang bermasalah.']);
                }

            } catch (\Exception $e) {
                Log::error('WhatsApp Exception: ' . $e->getMessage());
                return back()->withErrors(['nomor_whatsapp' => 'Gagal mengirim kode verifikasi karena kesalahan sistem.']);
            }

            // Redirect ke halaman verifikasi OTP
            $request->session()->put('user_id_for_verification', $user->id);
            return redirect()->route('whatsapp.verification.notice')
                ->with('success', 'Pendaftaran berhasil! Silakan periksa WhatsApp Anda.');
        } else {
            // Jika verifikasi tidak aktif, langsung loginkan
            Auth::login($user);
            return redirect($user->getDashboardUrl());
        }
    }

    /**
     * Memformat nomor telepon ke format E.164 yang diterima oleh WhatsApp.
     */
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