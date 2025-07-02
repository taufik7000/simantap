<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Filament\Warga\Resources\TicketResource;
use App\Models\PermohonanRevision;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL AKSI UNTUK KIRIM REVISI
            Actions\Action::make('kirim_revisi')
                ->label('Kirim Revisi')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                // Tampilkan tombol ini HANYA jika statusnya membutuhkan revisi dan belum ada revisi aktif
                ->visible(fn (): bool => $this->record->canBeRevised() && !$this->record->hasActiveRevision())
                ->form([
                    // Mengambil skema form dari logika sebelumnya
                    Forms\Components\Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi')
                        ->placeholder('Jelaskan perubahan atau perbaikan yang Anda lakukan...')
                        ->helperText('Contoh: Saya telah mengunggah ulang file KTP yang lebih jelas.')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('berkas_revisi')
                        ->label('Dokumen Revisi')
                        ->schema([
                            Forms\Components\TextInput::make('nama_dokumen')
                                ->label('Nama Dokumen')
                                ->required(),

                            Forms\Components\FileUpload::make('path_dokumen')
                                ->label('Pilih File Revisi')
                                ->disk('private')
                                ->directory('berkas-revisi') // Disimpan di folder terpisah
                                ->required(),
                        ])
                        ->addActionLabel('Tambah Dokumen')
                        ->columnSpanFull()
                        ->minItems(1),
                ])
                ->action(function (array $data): void {
                    // 1. Membuat record revisi baru
                    $revision = PermohonanRevision::create([
                        'permohonan_id' => $this->record->id,
                        'user_id' => Auth::id(),
                        'catatan_revisi' => $data['catatan_revisi'],
                        'berkas_revisi' => $data['berkas_revisi'],
                        'status' => 'pending', // Status awal revisi adalah 'pending' untuk direview petugas
                    ]);

                    // 2. Update status permohonan induk menjadi "sedang ditinjau" kembali
                    $this->record->update([
                        'status' => 'sedang_ditinjau',
                        'catatan_petugas' => 'Warga telah mengirimkan revisi dokumen. Menunggu review dari petugas.',
                    ]);

                    // 3. Kirim notifikasi ke pengguna
                    Notification::make()
                        ->title('Revisi Berhasil Dikirim!')
                        ->body("Revisi Anda untuk permohonan #{$this->record->kode_permohonan} akan segera kami tinjau.")
                        ->success()
                        ->sendToDatabase(Auth::user());
                        
                    // 4. Refresh data di halaman untuk memperbarui tampilan status dan menyembunyikan tombol
                    $this->refreshFormData();
                })
                ->modalHeading('Kirim Dokumen Perbaikan')
                ->modalDescription('Unggah dokumen perbaikan sesuai catatan dari petugas.')
                ->modalSubmitActionLabel('Kirim'),


            // --- Aksi-aksi yang sudah ada sebelumnya ---
            Actions\Action::make('create_ticket')
                ->label('Buat Tiket Bantuan')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('gray')
                ->url(fn () => TicketResource::getUrl('create', ['permohonan_id' => $this->record->id]))
                ->visible(fn () => !$this->record->hasActiveTickets())
                ->tooltip('Buat tiket bantuan jika ada masalah atau pertanyaan terkait permohonan ini.'),

            Actions\Action::make('view_tickets')
                ->label('Lihat Tiket Aktif')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn () => TicketResource::getUrl('index') . '?tableFilters[permohonan_id][value]=' . $this->record->id)
                ->visible(fn () => $this->record->hasActiveTickets())
                ->badge(fn () => $this->record->activeTickets()->count())
                ->badgeColor('warning')
                ->tooltip('Lihat tiket yang terkait dengan permohonan ini.'),
        ];
    }
}