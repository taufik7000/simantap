<?php

namespace App\Filament\Petugas\Resources\TicketResource\Pages;

use App\Filament\Petugas\Resources\TicketResource;
use App\Models\TicketMessage;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;
    protected static string $view = 'filament.petugas.pages.view-ticket';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendMessage')
                ->label('Kirim Balasan')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->form([
                    Textarea::make('message')
                        ->label('Balasan Anda')
                        ->required()
                        ->rows(4)
                        ->placeholder('Tulis balasan untuk pemohon...'),
                    FileUpload::make('attachments')
                        ->label('Lampiran (Opsional)')
                        ->multiple()
                        ->disk('private')
                        ->directory('ticket-attachments')
                        ->acceptedFileTypes(['image/*', 'application/pdf', '.doc', '.docx'])
                        ->maxSize(5120), // 5MB
                    Toggle::make('is_internal')
                        ->label('Pesan Internal')
                        ->helperText('Pesan internal hanya dapat dilihat oleh petugas, tidak oleh pemohon')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    TicketMessage::create([
                        'ticket_id' => $this->record->id,
                        'user_id' => Auth::id(),
                        'message' => $data['message'],
                        'attachments' => $data['attachments'] ?? null,
                        'is_internal' => $data['is_internal'] ?? false,
                    ]);

                    // Update status tiket jika belum ditangani
                    if ($this->record->status === 'open' && !$this->record->assigned_to) {
                        $this->record->update([
                            'status' => 'in_progress',
                            'assigned_to' => Auth::id(),
                        ]);
                    }

                    // Kirim notifikasi ke pemohon jika bukan pesan internal
                    if (!($data['is_internal'] ?? false)) {
                        Notification::make()
                            ->title('Balasan Baru untuk Tiket Anda')
                            ->body("Tim support telah membalas tiket #{$this->record->kode_tiket}")
                            ->success()
                            ->sendToDatabase($this->record->user);
                    }

                    Notification::make()
                        ->title('Pesan berhasil dikirim')
                        ->success()
                        ->send();

                    // Refresh halaman untuk menampilkan pesan baru
                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->modalHeading('Kirim Balasan')
                ->modalSubmitActionLabel('Kirim'),

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
                    ]);

                    // Kirim notifikasi ke pemohon
                    Notification::make()
                        ->title('Tiket Anda Telah Diselesaikan')
                        ->body("Tiket #{$this->record->kode_tiket} telah diselesaikan oleh tim support.")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Tiket berhasil diselesaikan')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->visible(fn () => in_array($this->record->status, ['open', 'in_progress'])),

            Actions\EditAction::make()
                ->label('Edit Tiket')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        // Tandai semua pesan dari pemohon sebagai sudah dibaca
        $this->record->messages()
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
