<?php

namespace App\Filament\Warga\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Warga\Resources\PermohonanRevisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPermohonanRevisions extends ListRecords
{
    protected static string $resource = PermohonanRevisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Kirim Revisi Baru')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Revisi')
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->count()),
                
            'pending' => Tab::make('Menunggu Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->where('status', 'pending')->count())
                ->badgeColor('warning'),
                
            'reviewed' => Tab::make('Sudah Direview')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['reviewed', 'accepted', 'rejected']))
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->whereIn('status', ['reviewed', 'accepted', 'rejected'])->count())
                ->badgeColor('success'),
        ];
    }
}