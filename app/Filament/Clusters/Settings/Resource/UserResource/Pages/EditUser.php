<?php

namespace App\Filament\Clusters\Settings\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Navigation\NavigationItem;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mendefinisikan sub-navigasi untuk halaman record.
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            NavigationItem::make('Detail Pengguna')
                ->url(UserResource::getUrl('view', ['record' => $page->getRecord()]))
                ->icon('heroicon-o-user-circle'),

            NavigationItem::make('Edit Pengguna')
                ->url(static::getUrl(['record' => $page->getRecord()]))
                ->isActiveWhen(fn () => $page instanceof EditUser)
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}