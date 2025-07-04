<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\FormulirMaster;
use App\Models\PermohonanRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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

        // Keamanan tambahan: Pastikan path file yang diminta benar-benar ada di record revisi ini
        $berkasRevisi = collect($revision->berkas_revisi);
        $berkasValid = $berkasRevisi->firstWhere('path_dokumen', $filePath);

        if (!$berkasValid) {
            abort(404, 'Berkas revisi tidak ditemukan.');
        }

        // Pastikan file ada di storage
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('private')->download($filePath);
    }

    public function downloadAll(Permohonan $permohonan): StreamedResponse|\Illuminate\Http\Response
    {
        $user = Auth::user();

        // Cek akses: pemilik permohonan atau petugas
        if ($user->id !== $permohonan->user_id && !$user->hasRole(['petugas', 'kadis', 'admin'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
        }

        // Buat nama file zip
        $zipFileName = "permohonan_{$permohonan->kode_permohonan}_" . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Pastikan direktori temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            
            // Tambahkan berkas permohonan awal
            if (is_array($permohonan->berkas_pemohon)) {
                foreach ($permohonan->berkas_pemohon as $index => $berkas) {
                    if (!empty($berkas['path_dokumen']) && Storage::disk('private')->exists($berkas['path_dokumen'])) {
                        $fileName = 'berkas_awal/' . ($berkas['nama_dokumen'] ?? 'dokumen_' . ($index + 1)) . '_' . basename($berkas['path_dokumen']);
                        $zip->addFile(Storage::disk('private')->path($berkas['path_dokumen']), $fileName);
                    }
                }
            }

            // Tambahkan berkas revisi
            $revisions = $permohonan->revisions()->orderBy('created_at', 'asc')->get();
            foreach ($revisions as $revision) {
                if (is_array($revision->berkas_revisi)) {
                    foreach ($revision->berkas_revisi as $index => $berkas) {
                        if (!empty($berkas['path_dokumen']) && Storage::disk('private')->exists($berkas['path_dokumen'])) {
                            $fileName = "revisi_{$revision->revision_number}/" . ($berkas['nama_dokumen'] ?? 'dokumen_' . ($index + 1)) . '_' . basename($berkas['path_dokumen']);
                            $zip->addFile(Storage::disk('private')->path($berkas['path_dokumen']), $fileName);
                        }
                    }
                }
            }

            // Tambahkan file informasi permohonan
            $info = "INFORMASI PERMOHONAN\n";
            $info .= "=====================\n\n";
            $info .= "Kode Permohonan: {$permohonan->kode_permohonan}\n";
            $info .= "Jenis Permohonan: " . ($permohonan->data_pemohon['jenis_permohonan'] ?? '-') . "\n";
            $info .= "Nama Pemohon: {$permohonan->user->name}\n";
            $info .= "NIK: {$permohonan->user->nik}\n";
            $info .= "Email: {$permohonan->user->email}\n";
            $info .= "Telepon: " . ($permohonan->user->nomor_telepon ?? '-') . "\n";
            $info .= "Status: {$permohonan->status}\n";
            $info .= "Tanggal Diajukan: " . $permohonan->created_at->format('d/m/Y H:i') . "\n";
            $info .= "Terakhir Update: " . $permohonan->updated_at->format('d/m/Y H:i') . "\n\n";

            if (!empty($permohonan->catatan_petugas)) {
                $info .= "CATATAN PETUGAS:\n";
                $info .= $permohonan->catatan_petugas . "\n\n";
            }

            if ($revisions->count() > 0) {
                $info .= "RIWAYAT REVISI:\n";
                $info .= "================\n\n";
                foreach ($revisions as $revision) {
                    $info .= "Revisi ke-{$revision->revision_number}\n";
                    $info .= "Tanggal: " . $revision->created_at->format('d/m/Y H:i') . "\n";
                    $info .= "Status: {$revision->status}\n";
                    if ($revision->catatan_revisi) {
                        $info .= "Catatan Warga: {$revision->catatan_revisi}\n";
                    }
                    if ($revision->catatan_petugas) {
                        $info .= "Catatan Petugas: {$revision->catatan_petugas}\n";
                    }
                    $info .= "\n";
                }
            }

            $zip->addFromString('info_permohonan.txt', $info);
            $zip->close();

            // Download file zip
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } else {
            abort(500, 'Gagal membuat file zip.');
        }
    }

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
}