<?php

namespace App\Filament\Warga\Resources\TicketResource\Pages;

use App\Filament\Warga\Resources\TicketResource;
use App\Models\TicketMessage;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;
    protected static string $view = 'filament.warga.pages.view-ticket';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendMessage')
                ->label('Kirim Pesan')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->form([
                    Textarea::make('message')
                        ->label('Pesan Anda')
                        ->required()
                        ->rows(4)
                        ->placeholder('Tulis pesan atau pertanyaan Anda...'),
                    FileUpload::make('attachments')
                        ->label('Lampiran (Opsional)')
                        ->multiple()
                        ->disk('private')
                        ->directory('ticket-attachments')
                        ->acceptedFileTypes(['image/*', 'application/pdf', '.doc', '.docx'])
                        ->maxSize(5120), // 5MB
                ])
                ->action(function (array $data): void {
                    TicketMessage::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => Auth::id(),
                        'message' => $data['message'],
                        'attachments' => $data['attachments'] ?? null,
                        'is_internal' => false,
                    ]);

                    // Update status tiket jika sudah resolved menjadi in_progress lagi
                    if ($this->record->status === 'resolved') {
                        $this->record->update(['status' => 'in_progress']);
                    }

                    Notification::make()
                        ->title('Pesan berhasil dikirim')
                        ->success()
                        ->send();

                    // Refresh halaman untuk menampilkan pesan baru
                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->modalHeading('Kirim Pesan Baru')
                ->modalSubmitActionLabel('Kirim')
                ->visible(fn () => $this->record->isActive()),
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
}