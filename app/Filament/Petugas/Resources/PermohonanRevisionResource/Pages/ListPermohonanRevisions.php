<?php

namespace App\Filament\Petugas\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Petugas\Resources\PermohonanRevisionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPermohonanRevisions extends ListRecords
{
    protected static string $resource = PermohonanRevisionResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Revisi')
                ->badge(fn () => $this->getModel()::count()),
                
            'pending' => Tab::make('Menunggu Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
                
            'reviewed' => Tab::make('Sudah Direview')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['reviewed', 'accepted', 'rejected']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['reviewed', 'accepted', 'rejected'])->count())
                ->badgeColor('success'),
        ];
    }
}