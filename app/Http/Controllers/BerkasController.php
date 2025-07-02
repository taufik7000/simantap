<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\FormulirMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BerkasController extends Controller
{
    public function download(Request $request): StreamedResponse|\Illuminate\Http\Response
    {
        // Ambil data dari query string URL
        $permohonanId = $request->query('permohonan_id');
        $filePath = $request->query('path');

        if (!$permohonanId || !$filePath) {
            abort(404, 'Permintaan tidak valid.');
        }

        $permohonan = Permohonan::findOrFail($permohonanId);

        // Memastikan apakah pengguna login atau tidak.
        // Jika pengguna login, maka lanjut ke logika berikutnya.
        $user = Auth::user();

        // Cek apakah user adalah pemilik permohonan ATAU seorang petugas.
        // Jika bukan, maka link download akan direct ke halaman login
        if ($user->id !== $permohonan->user_id && !$user->hasRole(['petugas', 'kadis', 'admin'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
        }

        // Keamanan tambahan: Pastikan path file yang diminta benar-benar ada di record permohonan ini
        $berkasPemohon = collect($permohonan->berkas_pemohon);
        $berkasValid = $berkasPemohon->firstWhere('path_dokumen', $filePath);

        if (!$berkasValid) {
            abort(404, 'Berkas tidak ditemukan pada permohonan ini.');
        }

        // Jika semua aman, kirim file untuk diunduh
        return Storage::disk('private')->download($filePath);
    }

    ## Cek Keamanan Download Formulir
    public function downloadMaster(FormulirMaster $formulirMaster): StreamedResponse
    {
        if (!$formulirMaster->file_path) {
            abort(404, 'File formulir tidak ditemukan.');
        }

        if (!Storage::disk('private')->exists($formulirMaster->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('private')->download($formulirMaster->file_path);
    }

    public function downloadRevision(Request $request): StreamedResponse|\Illuminate\Http\Response
    {
    $revisionId = $request->query('revision_id');
    $filePath = $request->query('path');

    if (!$revisionId || !$filePath) {
        abort(404, 'Permintaan tidak valid.');
    }

    $revision = PermohonanRevision::findOrFail($revisionId);
    $user = Auth::user();

    // Cek akses: pemilik revisi atau petugas
    if ($user->id !== $revision->user_id && !$user->hasRole(['petugas', 'kadis', 'admin'])) {
        abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
    }

    // Pastikan path file ada di record revisi
    $berkasRevisi = collect($revision->berkas_revisi);
    $berkasValid = $berkasRevisi->firstWhere('path_dokumen', $filePath);

    if (!$berkasValid) {
        abort(404, 'Berkas tidak ditemukan pada revisi ini.');
    }

    return Storage::disk('private')->download($filePath);
    }
}