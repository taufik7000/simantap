<?php

namespace App\Filament\Clusters\Settings\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\Page;
use Filament\Navigation\NavigationItem;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    /**
     * Mendefinisikan sub-navigasi untuk halaman record.
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            NavigationItem::make('Detail Pengguna')
                ->url(static::getUrl(['record' => $page->getRecord()]))
                ->isActiveWhen(fn () => $page instanceof ViewUser)
                ->icon('heroicon-o-user-circle'),

            NavigationItem::make('Edit Pengguna')
                ->url(UserResource::getUrl('edit', ['record' => $page->getRecord()]))
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}