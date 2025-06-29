<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Navigation\NavigationItem; // <-- Impor kelas ini
use Filament\Resources\Pages\Page; // <-- Impor kelas ini

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mendefinisikan sub-navigasi untuk halaman record.
     *
     * @param Page $page
     * @return array<NavigationItem>
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            NavigationItem::make('Edit User')
                ->label('Informasi Pengguna') // Label yang akan ditampilkan di menu
                ->url(static::getUrl(['record' => $page->getRecord()]))
                ->isActiveWhen(fn () => $page instanceof EditUser) // Aktif jika halaman saat ini adalah EditUser
                ->icon('heroicon-o-user-circle'),
        ];
    }
}