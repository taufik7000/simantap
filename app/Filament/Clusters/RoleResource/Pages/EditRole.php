<?php

namespace App\Filament\Clusters\Settings\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Navigation\NavigationItem; // Impor
use Filament\Resources\Pages\Page; // Impor

class EditRole extends EditRecord // <-- Pastikan nama kelas adalah EditRole
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Menambahkan sub-navigasi untuk halaman Role.
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            NavigationItem::make('Edit Role')
                ->label('Detail Peran')
                ->url(static::getUrl(['record' => $page->getRecord()]))
                ->isActiveWhen(fn () => $page instanceof EditRole)
                ->icon('heroicon-o-shield-check'),
        ];
    }
}