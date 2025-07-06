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

    public function getTitle(): string
    {
        $kodePermohonan = $this->record->kode_permohonan;
        $jenisPermohonan = $this->record->data_pemohon['jenis_permohonan'] ?? 'Tidak Diketahui';
        
        return "#{$kodePermohonan} - Permohonan {$jenisPermohonan}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kirim_revisi')
                ->label('Kirim Revisi')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn (): bool => $this->record->canBeRevised())
                ->form([
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
                                ->directory('berkas-revisi')
                                ->required(),
                        ])
                        ->addActionLabel('Tambah Dokumen')
                        ->columnSpanFull()
                        ->minItems(1),
                ])
                ->action(function (array $data): void {
                    PermohonanRevision::create([
                        'permohonan_id' => $this->record->id,
                        'user_id' => Auth::id(),
                        'catatan_revisi' => $data['catatan_revisi'],
                        'berkas_revisi' => $data['berkas_revisi'],
                        'status' => 'pending',
                    ]);

                    $this->record->update([
                        'status' => 'sedang_ditinjau',
                        'catatan_petugas' => 'Warga telah mengirimkan revisi dokumen. Menunggu review dari petugas.',
                    ]);

                    Notification::make()
                        ->title('Revisi Berhasil Dikirim!')
                        ->body("Revisi Anda untuk permohonan #{$this->record->kode_permohonan} akan segera kami tinjau.")
                        ->success()
                        ->sendToDatabase(Auth::user());
                        
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
                })
                ->modalHeading('Kirim Dokumen Perbaikan')
                ->modalDescription('Unggah dokumen perbaikan sesuai catatan dari petugas.')
                ->modalSubmitActionLabel('Kirim'),

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