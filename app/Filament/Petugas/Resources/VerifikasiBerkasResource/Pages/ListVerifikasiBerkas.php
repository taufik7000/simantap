<?php

namespace App\Filament\Petugas\Resources\VerifikasiBerkasResource\Pages;

use App\Filament\Petugas\Resources\VerifikasiBerkasResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVerifikasiBerkas extends ListRecords
{
    protected static string $resource = VerifikasiBerkasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Definisikan tab-tab untuk filtering.
     */
    public function getTabs(): array
    {
        $baseQuery = $this->getResource()::getEloquentQuery();

        return [
            'semua' => Tab::make('Semua Tugas')
                // Untuk badge, kita clone query agar tidak terpengaruh filter tab lain
                ->badge(fn() => (clone $baseQuery)->count()),

            'perlu_verifikasi' => Tab::make('Perlu Diverifikasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['verifikasi_berkas', 'diperbaiki_warga']))
                ->badge(fn() => (clone $baseQuery)->whereIn('status', ['verifikasi_berkas', 'diperbaiki_warga'])->count())
                ->badgeColor('warning'),

            'menunggu_warga' => Tab::make('Menunggu Perbaikan dari Warga')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'butuh_perbaikan'))
                ->badge(fn() => (clone $baseQuery)->where('status', 'butuh_perbaikan')->count())
                ->badgeColor('danger'),
        ];
    }

    /**
     * Atur tab 'Perlu Diverifikasi' sebagai default.
     */
    public function getDefaultActiveTab(): string
    {
        return 'perlu_verifikasi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode Permohonan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Nama Warga')->searchable(),

                // Tambahkan kolom status untuk kejelasan
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verifikasi_berkas' => 'warning',
                        'diperbaiki_warga' => 'info',
                        'butuh_perbaikan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'verifikasi_berkas' => 'Menunggu Verifikasi',
                        'diperbaiki_warga' => 'Telah Diperbaiki Warga',
                        'butuh_perbaikan' => 'Menunggu Revisi',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('layanan.name')->label('Jenis Layanan')->wrap(),
                Tables\Columns\TextColumn::make('updated_at')->label('Update Terakhir')->since()->sortable(),
            ])
            ->actions([
                // Arahkan tombol view ke halaman view kustom kita
                Tables\Actions\ViewAction::make()->url(fn ($record): string =>
                    VerifikasiBerkasResource::getUrl('view', ['record' => $record])
                ),
            ])
            ->defaultSort('updated_at', 'desc') // Urutkan berdasarkan update terakhir agar lebih relevan
            ->emptyStateHeading('Tidak ada tugas dalam tab ini')
            ->emptyStateDescription('Silakan periksa tab lain atau tunggu tugas baru masuk.');
    }
}