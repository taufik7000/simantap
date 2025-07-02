<?php

namespace App\Filament\Kadis\Resources\WebsiteContentResource\Pages;

use App\Filament\Kadis\Resources\WebsiteContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebsiteContent extends EditRecord
{
    protected static string $resource = WebsiteContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('website.page', $this->record->slug))
                ->openUrlInNewTab()
                ->color('info'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}