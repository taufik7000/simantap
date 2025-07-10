<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPermohonan: string implements HasColor, HasIcon, HasLabel
{
    // --- Tahap Awal Pengajuan ---
    case BARU = 'baru';
    case SEDANG_DITINJAU = 'sedang_ditinjau';

    // --- Tahap Verifikasi oleh Petugas ---
    case VERIFIKASI_BERKAS = 'verifikasi_berkas';
    case DIPERBAIKI_WARGA = 'diperbaiki_warga';

    // --- Tahap Pengerjaan oleh Petugas ---
    case MENUNGGU_ENTRI_DATA = 'menunggu_entri_data';
    case PROSES_ENTRI = 'proses_entri';
    case ENTRI_DATA_SELESAI = 'entri_data_selesai';

    // --- Tahap Persetujuan & Penyelesaian ---
    case MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';
    case DISETUJUI = 'disetujui';
    case DOKUMEN_DITERBITKAN = 'dokumen_diterbitkan';
    case SELESAI = 'selesai';

    // --- Status Khusus (Loop atau Final) ---
    case BUTUH_PERBAIKAN = 'butuh_perbaikan';
    case DITOLAK = 'ditolak';
    case DIBATALKAN = 'dibatalkan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BARU => 'Baru Diajukan',
            self::SEDANG_DITINJAU => 'Dalam Peninjauan',
            self::VERIFIKASI_BERKAS => 'Verifikasi Berkas',
            self::DIPERBAIKI_WARGA => 'Telah Diperbaiki Warga',
            self::MENUNGGU_ENTRI_DATA => 'Menunggu Entri Data',
            self::PROSES_ENTRI => 'Proses Entri Data',
            self::ENTRI_DATA_SELESAI => 'Entri Data Selesai',
            self::MENUNGGU_PERSETUJUAN => 'Menunggu Persetujuan',
            self::DISETUJUI => 'Disetujui',
            self::DOKUMEN_DITERBITKAN => 'Dokumen Diterbitkan',
            self::SELESAI => 'Selesai',
            self::BUTUH_PERBAIKAN => 'Butuh Perbaikan',
            self::DITOLAK => 'Ditolak',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BARU, self::SEDANG_DITINJAU, self::DIBATALKAN => 'primary',
            self::VERIFIKASI_BERKAS, self::PROSES_ENTRI => 'info',
            self::DIPERBAIKI_WARGA, self::ENTRI_DATA_SELESAI => 'warning',
            self::MENUNGGU_ENTRI_DATA, self::MENUNGGU_PERSETUJUAN => 'primary',
            self::DISETUJUI, self::DOKUMEN_DITERBITKAN, self::SELESAI => 'success',
            self::BUTUH_PERBAIKAN, self::DITOLAK => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::BARU => 'heroicon-o-paper-airplane',
            self::SEDANG_DITINJAU => 'heroicon-o-magnifying-glass',
            self::VERIFIKASI_BERKAS => 'heroicon-o-document-magnifying-glass',
            self::DIPERBAIKI_WARGA => 'heroicon-o-chat-bubble-left-right',
            self::MENUNGGU_ENTRI_DATA => 'heroicon-o-pencil-square',
            self::PROSES_ENTRI => 'heroicon-o-computer-desktop',
            self::ENTRI_DATA_SELESAI => 'heroicon-o-check',
            self::MENUNGGU_PERSETUJUAN => 'heroicon-o-user-group',
            self::DISETUJUI => 'heroicon-o-check-badge',
            self::DOKUMEN_DITERBITKAN => 'heroicon-o-printer',
            self::SELESAI => 'heroicon-o-flag',
            self::BUTUH_PERBAIKAN => 'heroicon-o-arrow-uturn-left',
            self::DITOLAK => 'heroicon-o-x-circle',
            self::DIBATALKAN => 'heroicon-o-no-symbol',
        };
    }
}