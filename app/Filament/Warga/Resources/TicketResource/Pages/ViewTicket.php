<?php

namespace App\Filament\Warga\Resources\TicketResource\Pages;

use App\Filament\Warga\Resources\TicketResource;
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
    protected static string $view = 'filament.warga.pages.view-ticket';

    // Properties untuk inline chat
    public $messageText = '';
    public $attachments = [];

    protected function getHeaderActions(): array
    {
        return [
            // Hapus action sendMessage karena sekarang menggunakan inline form
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        // Tandai semua pesan dari petugas sebagai sudah dibaca
        $this->record->messages()
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
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
            'is_internal' => false,
        ]);

        // Update status tiket jika sudah resolved menjadi in_progress lagi
        if ($this->record->status === 'resolved') {
            $this->record->update(['status' => 'in_progress']);
        }

        // Kirim notifikasi ke petugas yang menangani (jika ada)
        if ($this->record->assigned_to) {
            Notification::make()
                ->title('Pesan Baru dari Warga')
                ->body("Warga telah mengirim pesan baru pada tiket #{$this->record->kode_tiket}")
                ->info()
                ->sendToDatabase($this->record->assignedTo);
        }

        // Reset form
        $this->messageText = '';
        $this->attachments = [];

        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Pesan berhasil dikirim')
            ->body('Pesan Anda telah dikirim ke tim support.')
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