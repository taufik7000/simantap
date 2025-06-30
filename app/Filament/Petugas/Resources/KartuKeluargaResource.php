<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\KartuKeluargaResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KartuKeluargaResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Kependudukan';
    protected static ?string $modelLabel = 'Kartu Keluarga';
    protected static ?string $pluralModelLabel = 'Kartu Keluarga';
    protected static ?string $slug = 'kartu-keluarga';
    protected static ?string $recordRouteKeyName = 'nomor_kk';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status_keluarga', 'Kepala Keluarga');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Kepala Keluarga')->searchable(),
                TextColumn::make('nik')->label('NIK')->searchable(),
                TextColumn::make('nomor_kk')->label('Nomor KK')->searchable(),
                TextColumn::make('alamat')->label('Alamat')->limit(40)->tooltip(fn ($state) => $state),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Kartu Keluarga')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextEntry::make('nomor_kk')->label('Nomor KK')->icon('heroicon-s-document-text'),
                        TextEntry::make('anggota_keluarga_count')->label('Jumlah Anggota')->state(fn (Model $record) => $record->anggotaKeluarga()->count() . ' orang')->icon('heroicon-s-users'),
                        TextEntry::make('name')->label('Kepala Keluarga')->icon('heroicon-s-user'),
                        TextEntry::make('nik')->label('NIK Kepala Keluarga')->icon('heroicon-s-identification'),
                    ])->columns(2),

                Section::make('Alamat')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        TextEntry::make('rt_rw')->label('RT/RW'),
                        TextEntry::make('desa_kelurahan')->label('Desa/Kelurahan'),
                        TextEntry::make('kecamatan')->label('Kecamatan'),
                        TextEntry::make('kabupaten')->label('Kabupaten'),
                        TextEntry::make('alamat')->label('Alamat Lengkap')->columnSpanFull(),
                    ])->columns(4),
                
                Section::make('Anggota Keluarga')
                    ->icon('heroicon-o-users')
                    ->schema([
                        RepeatableEntry::make('anggotaKeluarga')
                            ->label('')
                            ->schema([
                                Grid::make(2)->schema([
                                    // Kolom untuk informasi
                                    Section::make()->schema([
                                        TextEntry::make('name')->label('Nama Anggota'),
                                        TextEntry::make('nik')->label('NIK'),
                                        TextEntry::make('status_keluarga')->label('Status')->badge(),
                                    ])->columns(3),
                                    
                                    // Kolom untuk tombol aksi
                                    Actions::make([
                                        Action::make('lihatDetail')
                                            ->label('Lihat Detail')
                                            ->icon('heroicon-s-eye')
                                            // Arahkan ke halaman view di UserResource
                                            ->url(fn (User $record) => UserResource::getUrl('view', ['record' => $record]))
                                            ->color('gray'),
                                    ])->alignCenter(),
                                ]),
                            ])
                            ->contained(false),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKartuKeluargas::route('/'),
            'view' => Pages\ViewKartuKeluarga::route('/{record:nomor_kk}'),
        ];
    }
}