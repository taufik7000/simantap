<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use App\Models\PermohonanRevision;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickActionsController extends Controller
{
    public function quickStatusUpdate(Request $request)
    {
        $request->validate([
            'permohonan_id' => 'required|exists:permohonans,id',
            'status' => 'required|in:verifikasi_berkas,diproses,disetujui'
        ]);

        // Validasi akses menggunakan hasRole dari Spatie
        if (!Auth::user()->hasRole(['petugas', 'admin', 'kadis'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $permohonan = Permohonan::findOrFail($request->permohonan_id);

        $defaultMessages = [
            'verifikasi_berkas' => 'Sedang melakukan verifikasi kelengkapan berkas.',
            'diproses' => 'Permohonan sedang dalam proses penyelesaian.',
            'disetujui' => 'Selamat! Permohonan Anda telah disetujui.',
        ];

        $permohonan->update([
            'status' => $request->status,
            'catatan_petugas' => $defaultMessages[$request->status] ?? 'Status permohonan diperbarui.',
        ]);

        $statusLabels = [
            'verifikasi_berkas' => 'Verifikasi Berkas',
            'diproses' => 'Sedang Diproses',
            'disetujui' => 'Disetujui',
        ];

        // Kirim notifikasi ke warga menggunakan Filament Notifications
        try {
            Notification::make()
                ->title('Status Permohonan Diperbarui')
                ->body("Status permohonan {$permohonan->kode_permohonan} diubah menjadi: {$statusLabels[$request->status]}")
                ->success()
                ->sendToDatabase($permohonan->user);
        } catch (\Exception $e) {
            // Jika notifikasi gagal, log error tapi jangan stop proses
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }

        return back()->with('success', "Status berhasil diubah menjadi: {$statusLabels[$request->status]}");
    }

    public function quickRevisionAction(Request $request)
    {
        $request->validate([
            'revision_id' => 'required|exists:permohonan_revisions,id',
            'action' => 'required|in:approve,reject'
        ]);

        // Validasi akses menggunakan hasRole dari Spatie
        if (!Auth::user()->hasRole(['petugas', 'admin', 'kadis'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $revision = PermohonanRevision::findOrFail($request->revision_id);

        if ($request->action === 'approve') {
            // PERBAIKAN: Gunakan 'accepted' sesuai database enum
            $revision->update([
                'status' => 'accepted',
                'catatan_petugas' => 'Revisi diterima dan akan diproses lebih lanjut.',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

            // Update status permohonan induk
            $revision->permohonan->update([
                'status' => 'diproses',
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} telah diterima. Permohonan akan diproses lebih lanjut.",
            ]);

            // Kirim notifikasi ke warga
            try {
                Notification::make()
                    ->title('Revisi Anda Telah Diterima')
                    ->body("Revisi ke-{$revision->revision_number} telah diterima dan akan diproses lebih lanjut.")
                    ->success()
                    ->sendToDatabase($revision->user);
            } catch (\Exception $e) {
                \Log::error('Failed to send notification: ' . $e->getMessage());
            }

            return back()->with('success', 'Revisi telah berhasil diterima.');

        } else { // reject
            // Untuk reject, redirect ke halaman dengan form alasan
            return redirect()->back()->with([
                'reject_revision_id' => $revision->id,
                'reject_revision_number' => $revision->revision_number
            ]);
        }
    }

    public function quickRevisionReject(Request $request)
    {
        $request->validate([
            'revision_id' => 'required|exists:permohonan_revisions,id',
            'reject_reason' => 'required|string|max:1000'
        ]);

        // Validasi akses
        if (!Auth::user()->hasRole(['petugas', 'admin', 'kadis'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $revision = PermohonanRevision::findOrFail($request->revision_id);

        $revision->update([
            'status' => 'rejected',
            'catatan_petugas' => $request->reject_reason,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        // Update status permohonan induk
        $revision->permohonan->update([
            'status' => 'membutuhkan_revisi',
            'catatan_petugas' => "Revisi ke-{$revision->revision_number} ditolak. " . $request->reject_reason,
        ]);

        // Kirim notifikasi ke warga
        try {
            Notification::make()
                ->title('Revisi Perlu Diperbaiki')
                ->body("Revisi ke-{$revision->revision_number} ditolak. " . $request->reject_reason)
                ->warning()
                ->sendToDatabase($revision->user);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Revisi telah ditolak dengan catatan.');
    }
}