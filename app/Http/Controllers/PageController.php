<?php

namespace App\Http\Controllers;

use App\Models\KategoriLayanan;
use App\Models\Permohonan;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Menampilkan halaman publik yang berisi semua layanan yang aktif.
     */
    public function semuaLayanan()
    {
        // Ambil semua kategori yang memiliki setidaknya satu layanan yang aktif.
        // Untuk setiap kategori, muat juga relasi 'layanans' tetapi HANYA yang aktif.
        $kategoriLayanans = KategoriLayanan::whereHas('layanans', function ($query) {
            $query->where('is_active', true);
        })->with(['layanans' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        // Kirim data ke view
        return view('pages.semua-layanan', [
            'kategoriLayanans' => $kategoriLayanans
        ]);
    }

    public function lacakPermohonan(Request $request)
    {
        $permohonan = null;
        $kode_permohonan = $request->query('kode_permohonan');

        // Jika ada kode permohonan di URL, cari datanya
        if ($kode_permohonan) {
            $permohonan = Permohonan::with('layanan', 'logs.user')
                ->where('kode_permohonan', $kode_permohonan)
                ->first();
        }

        return view('pages.lacak-permohonan', [
            'permohonan' => $permohonan
        ]);
    }
}