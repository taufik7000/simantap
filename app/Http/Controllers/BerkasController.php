<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Models\FormulirMaster;
use App\Models\PermohonanRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;
use ZipArchive;

class BerkasController extends Controller
{
public function download(Request $request): StreamedResponse|Response
{
    $permohonanId = $request->query('permohonan_id');
    $filePath = $request->query('path');

    if (!$permohonanId || !$filePath) {
        abort(404, 'Permintaan tidak valid.');
    }

    $permohonan = Permohonan::findOrFail($permohonanId);
    $user = Auth::user();

    if ($user->id !== $permohonan->user_id && !$user->hasRole(['petugas', 'kadis', 'admin'])) {
        abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
    }

    // Keamanan tambahan: Pastikan path file yang diminta benar-benar ada di record permohonan ini
    $berkasPemohon = $permohonan->berkas_pemohon;
    $pathIsValid = false;

    if (is_array($berkasPemohon)) {
        // Cek untuk struktur baru: ['file_key' => 'path/to/file.pdf']
        if (in_array($filePath, $berkasPemohon, true)) {
            $pathIsValid = true;
        } 
        // Cek untuk struktur lama (fallback): [['path_dokumen' => 'path/to/file.pdf']]
        else {
            foreach ($berkasPemohon as $berkas) {
                if (is_array($berkas) && isset($berkas['path_dokumen']) && $berkas['path_dokumen'] === $filePath) {
                    $pathIsValid = true;
                    break;
                }
            }
        }
    }

    if (!$pathIsValid) {
        abort(404, 'Berkas tidak ditemukan pada permohonan ini.');
    }

    if (!Storage::disk('private')->exists($filePath)) {
        abort(404, 'File tidak ditemukan di server.');
    }

    return Storage::disk('private')->download($filePath);
}

    public function downloadRevision(Request $request): StreamedResponse|Response
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
    /**
     * Download semua berkas permohonan dalam format ZIP
     * * @param Permohonan $permohonan
     * @return BinaryFileResponse
     */
    public function downloadAll(Permohonan $permohonan): BinaryFileResponse
    {
        $user = Auth::user();

        if ($user->id !== $permohonan->user_id && !$user->hasRole(['petugas', 'kadis', 'admin'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk berkas ini.');
        }

        $zipFileName = "permohonan_{$permohonan->kode_permohonan}_" . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

    $zip = new ZipArchive;
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        
        // Tambahkan berkas permohonan awal - PERBAIKAN DI SINI
        $jenisPermohonan = $permohonan->data_pemohon['jenis_permohonan'] ?? null;
        $fileRequirements = [];
        
        // Ambil file requirements dari layanan (sama seperti di ViewPermohonan)
        if ($jenisPermohonan && $permohonan->layanan?->description) {
            $jenisData = collect($permohonan->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);
            $fileRequirements = $jenisData['file_requirements'] ?? [];
        }

        // Loop berkas pemohon dengan struktur yang benar
        if (is_array($permohonan->berkas_pemohon)) {
            foreach ($permohonan->berkas_pemohon as $fileKey => $filePath) {
                if (!empty($filePath) && Storage::disk('private')->exists($filePath)) {
                    // Cari nama file dari requirements
                    $fileName = 'dokumen_' . $fileKey;
                    foreach ($fileRequirements as $requirement) {
                        if ($requirement['file_key'] === $fileKey) {
                            $fileName = $requirement['file_name'];
                            break;
                        }
                    }
                    
                    // Bersihkan nama file dan tambahkan ke zip
                    $cleanFileName = preg_replace('/[\/\\\\]/', '_', $fileName);
                    $cleanFileName = preg_replace('/[^a-zA-Z0-9\s\-_().]/', '', $cleanFileName);
                    $cleanFileName = trim($cleanFileName);
                    if (empty($cleanFileName)) {
                        $cleanFileName = 'dokumen_' . $fileKey;
                    }
                    
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                    $fileNameInZip = 'berkas-awal/' . $cleanFileName . '.' . $extension;
                    $zip->addFile(Storage::disk('private')->path($filePath), $fileNameInZip);
                }
            }
        }

            // ... (Sisa kode untuk revisi dan info permohonan tidak perlu diubah)
            $revisions = $permohonan->revisions()->orderBy('created_at', 'asc')->get();
            foreach ($revisions as $revision) {
                if (is_array($revision->berkas_revisi)) {
                    foreach ($revision->berkas_revisi as $index => $berkas) {
                        if (!empty($berkas['path_dokumen']) && Storage::disk('private')->exists($berkas['path_dokumen'])) {
                            $fileName = $berkas['nama_dokumen'] ?? ('dokumen_' . ($index + 1));
                            $cleanFileName = preg_replace('/[\/\\\\]/', '_', $fileName);
                            $cleanFileName = preg_replace('/[^a-zA-Z0-9\s\-_().]/', '', $cleanFileName);
                            $cleanFileName = trim($cleanFileName);
                            if (empty($cleanFileName)) {
                                $cleanFileName = 'dokumen_' . ($index + 1);
                            }
                            $extension = pathinfo($berkas['path_dokumen'], PATHINFO_EXTENSION);
                            $pathInZip = "revisi_{$revision->revision_number}/" . $cleanFileName . '.' . $extension;
                            $zip->addFile(Storage::disk('private')->path($berkas['path_dokumen']), $pathInZip);
                        }
                    }
                }
            }

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
            $info .= "BERKAS YANG DISERTAKAN:\n";
            $info .= "========================\n\n";
            $info .= "BERKAS AWAL:\n";
            if (is_array($permohonan->berkas_permohonan) && count($permohonan->berkas_permohonan) > 0) {
                $jenisPermohonan = $permohonan->data_pemohon['jenis_permohonan'] ?? null;
                $fileRequirements = [];
                if ($jenisPermohonan && $permohonan->layanan?->description) {
                    $jenisData = collect($permohonan->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);
                    $fileRequirements = $jenisData['file_requirements'] ?? [];
                }
                foreach ($permohonan->berkas_permohonan as $fileKey => $filePath) {
                    if (!empty($filePath)) {
                        $fileName = 'dokumen_' . $fileKey;
                        foreach ($fileRequirements as $requirement) {
                            if ($requirement['file_key'] === $fileKey) {
                                $fileName = $requirement['file_name'];
                                break;
                            }
                        }
                        $info .= "- {$fileName}: " . (Storage::disk('private')->exists($filePath) ? 'Tersedia' : 'File tidak ditemukan') . "\n";
                    }
                }
            } else {
                $info .= "- Tidak ada berkas awal\n";
            }
            if ($revisions->count() > 0) {
                $info .= "\nRIWAYAT REVISI:\n";
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
                    if (is_array($revision->berkas_revisi) && count($revision->berkas_revisi) > 0) {
                        $info .= "Berkas Revisi:\n";
                        foreach ($revision->berkas_revisi as $berkas) {
                            $nama = $berkas['nama_dokumen'] ?? 'Dokumen tanpa nama';
                            $info .= "  - {$nama}\n";
                        }
                    }
                    $info .= "\n";
                }
            }
            
            $zip->addFromString('info_permohonan.txt', $info);
            $zip->close();

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

    /**
 * Download dokumen profil warga dengan aman.
 */
public function downloadProfileDocument(Request $request): StreamedResponse|Response
{
    $userId = $request->query('user_id');
    $field = $request->query('field');
    $validFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];

    if (!$userId || !$field || !in_array($field, $validFields)) {
        abort(404, 'Permintaan tidak valid.');
    }

    $owner = User::findOrFail($userId);
    $filePath = $owner->{$field};

    if (!$filePath) {
        abort(404, 'File tidak ditemukan untuk pengguna ini.');
    }

    // Hanya admin/petugas yang bisa mengunduh
    if (!Auth::user()->hasAnyRole(['petugas', 'admin', 'kadis'])) {
        abort(403, 'Anda tidak memiliki hak akses.');
    }

    if (!Storage::disk('private')->exists($filePath)) {
        abort(404, 'File tidak ditemukan di server.');
    }

    return Storage::disk('private')->download($filePath);
}
}