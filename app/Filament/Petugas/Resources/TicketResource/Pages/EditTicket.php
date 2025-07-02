<?php

namespace App\Filament\Petugas\Resources\TicketResource\Pages;

use App\Filament\Petugas\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function afterSave(): void
    {
        $ticket = $this->record;
        
        // Check if status was changed
        if ($this->record->wasChanged('status')) {
            $oldStatus = $this->record->getOriginal('status');
            $newStatus = $this->record->status;
            
            $this->sendStatusUpdateNotification($oldStatus, $newStatus);
        }

        // Check if assigned_to was changed
        if ($this->record->wasChanged('assigned_to')) {
            $this->sendAssignmentNotification();
        }
    }

    protected function sendStatusUpdateNotification(string $oldStatus, string $newStatus): void
    {
        $ticket = $this->record;
        $user = $ticket->user;
        
        if (!$user) {
            return;
        }

        $statusLabels = [
            'open' => 'Terbuka',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Terselesaikan',
            'closed' => 'Ditutup',
        ];

        $newStatusLabel = $statusLabels[$newStatus] ?? $newStatus;
        $notificationIcon = match($newStatus) {
            'resolved' => 'heroicon-o-check-circle',
            'closed' => 'heroicon-o-x-circle',
            'in_progress' => 'heroicon-o-clock',
            default => 'heroicon-o-information-circle',
        };

        $notificationColor = match($newStatus) {
            'resolved' => 'success',
            'closed' => 'gray',
            'in_progress' => 'info',
            default => 'info',
        };

        $body = "Status tiket #{$ticket->kode_tiket} telah diperbarui menjadi: {$newStatusLabel}";

        // Send notification to user
        Notification::make()
            ->title('Status Tiket Diperbarui')
            ->icon($notificationIcon)
            ->body($body)
            ->color($notificationColor)
            ->sendToDatabase($user);

        // Log the activity
        activity()
            ->performedOn($ticket)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ])
            ->log("Status tiket diubah dari {$oldStatus} ke {$newStatus}");
    }

    protected function sendAssignmentNotification(): void
    {
        $ticket = $this->record;
        $assignedUser = $ticket->assignedTo;
        
        if ($assignedUser && $assignedUser->id !== Auth::id()) {
            Notification::make()
                ->title('Tiket Baru Ditugaskan')
                ->body("Tiket #{$ticket->kode_tiket} telah ditugaskan kepada Anda")
                ->info()
                ->sendToDatabase($assignedUser);
        }
    }
}