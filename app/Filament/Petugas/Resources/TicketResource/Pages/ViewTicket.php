<?php

namespace App\Filament\Petugas\Resources\TicketResource\Pages;

use App\Filament\Petugas\Resources\TicketResource;
use App\Models\TicketMessage;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ViewTicket extends ViewRecord
{
    use WithFileUploads;

    protected static string $resource = TicketResource::class;
    protected static string $view = 'filament.petugas.pages.view-ticket';

    // Properties untuk inline chat
    public $messageText = '';
    public $attachments = [];
    public $isInternal = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('assign_to_me')
                ->label('Ambil Tiket')
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->action(function (): void {
                    $this->record->update([
                        'assigned_to' => Auth::id(),
                        'status' => $this->record->status === 'open' ? 'in_progress' : $this->record->status,
                    ]);

                    Notification::make()
                        ->title('Tiket berhasil diambil')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->visible(fn () => !$this->record->assigned_to),

            Actions\Action::make('resolve')
                ->label('Tandai Selesai')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Tandai Tiket Selesai')
                ->modalDescription('Apakah Anda yakin tiket ini sudah selesai ditangani?')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                        'assigned_to' => $this->record->assigned_to ?? Auth::id(),
                    ]);

                    // Kirim notifikasi ke pemohon
                    Notification::make()
                        ->title('Tiket Telah Diselesaikan')
                        ->body("Tiket #{$this->record->kode_tiket} telah diselesaikan oleh tim support.")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Tiket berhasil diselesaikan')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status !== 'resolved'),

            Actions\Action::make('reopen')
                ->label('Buka Kembali')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'in_progress',
                        'resolved_at' => null,
                    ]);

                    Notification::make()
                        ->title('Tiket berhasil dibuka kembali')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'resolved'),
        ];
    }

    public function sendMessage()
    {
        // Validasi input
        $this->validate([
            'messageText' => 'required|string|max:5000',
            'attachments.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx',
        ], [
            'messageText.required' => 'Pesan tidak boleh kosong.',
            'messageText.max' => 'Pesan tidak boleh lebih dari 5000 karakter.',
            'attachments.*.max' => 'Ukuran file tidak boleh lebih dari 5MB.',
            'attachments.*.mimes' => 'File harus berformat: jpg, jpeg, png, gif, pdf, doc, docx.',
        ]);

        // Proses file attachments
        $attachmentPaths = [];
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                if ($attachment instanceof TemporaryUploadedFile) {
                    $path = $attachment->store('ticket-attachments', 'private');
                    $attachmentPaths[] = $path;
                }
            }
        }

        // Simpan pesan ke database
        TicketMessage::create([
            'ticket_id' => $this->record->id,
            'user_id' => Auth::id(),
            'message' => $this->messageText,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'is_internal' => $this->isInternal,
        ]);

        // Update status tiket jika belum ditangani
        if ($this->record->status === 'open' && !$this->record->assigned_to) {
            $this->record->update([
                'status' => 'in_progress',
                'assigned_to' => Auth::id(),
            ]);
        }

        // Kirim notifikasi ke pemohon jika bukan pesan internal
        if (!$this->isInternal) {
            Notification::make()
                ->title('Balasan Baru untuk Tiket Anda')
                ->body("Tim support telah membalas tiket #{$this->record->kode_tiket}")
                ->success()
                ->sendToDatabase($this->record->user);
        }

        // Reset form
        $this->messageText = '';
        $this->attachments = [];
        $this->isInternal = false;

        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Pesan berhasil dikirim')
            ->body($this->isInternal ? 'Pesan internal berhasil dikirim.' : 'Balasan berhasil dikirim ke warga.')
            ->success()
            ->send();

        // Refresh data record untuk menampilkan pesan baru
        $this->record->refresh();
        $this->record->load('messages.user');

        // Dispatch event untuk auto-scroll
        $this->dispatch('message-sent');
    }

    protected function getListeners(): array
    {
        return [
            'refresh-messages' => '$refresh',
        ];
    }
}