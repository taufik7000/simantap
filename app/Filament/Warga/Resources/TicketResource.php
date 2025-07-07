<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\Permohonan;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Tiket Bantuan';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Pilihan: Buat tiket berdasarkan permohonan yang sudah ada ATAU berdasarkan jenis layanan saja
                Forms\Components\Tabs::make('ticket_type')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('permohonan_existing')
                            ->label('Terkait Permohonan Saya')
                            ->icon('heroicon-o-bookmark')
                            ->schema([
                                Forms\Components\Select::make('permohonan_id')
                                    ->label('Pilih Permohonan')
                                    ->options(function () {
                                        return Permohonan::where('user_id', Auth::id())
                                            ->with('layanan')
                                            ->get()
                                            ->mapWithKeys(function ($permohonan) {
                            // Ambil jenis permohonan dari data_pemohon
                                            $jenisPermohonan = $permohonan->data_pemohon['jenis_permohonan'] ?? 'Tidak ada jenis permohonan';
                            
                                            return [
                                                $permohonan->id => $permohonan->kode_permohonan . ' - ' . $jenisPermohonan
                                            ];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => request()->query('permohonan_id'))
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $permohonan = Permohonan::find($state);
                                            if ($permohonan && $permohonan->layanan) {
                                                $set('layanan_id', $permohonan->layanan->id);
                                            }
                                        }
                                    })
                                    ->helperText('Pilih permohonan yang sudah Anda ajukan sebelumnya'),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('layanan_general')
                            ->label('Pertanyaan Umum Layanan')
                            ->icon('heroicon-s-question-mark-circle')
                            ->schema([
                                Forms\Components\Select::make('layanan_id')
                                    ->label('Jenis Layanan')
                                    ->options(function () {
                                        return Layanan::where('is_active', true)
                                            ->with('kategoriLayanan')
                                            ->get()
                                            ->mapWithKeys(function ($layanan) {
                                                return [$layanan->id => $layanan->kategoriLayanan->name . ' - ' . $layanan->name];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Reset permohonan_id jika user memilih layanan langsung
                                        $set('permohonan_id', null);
                                    })
                                    ->helperText('Pilih jenis layanan yang ingin Anda tanyakan'),
                            ]),
                    ])
                    ->columnSpanFull(),

                // Hidden field untuk layanan_id (akan terisi otomatis)
                Forms\Components\Hidden::make('layanan_id'),

                Forms\Components\TextInput::make('subject')
                    ->label('Judul Tiket')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ringkas masalah atau pertanyaan Anda')
                    ->helperText('Contoh: "Kesulitan upload dokumen KTP" atau "Status permohonan tidak berubah"'),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Masalah')
                    ->required()
                    ->rows(5)
                    ->placeholder('Jelaskan masalah atau pertanyaan Anda secara detail...')
                    ->helperText('Semakin detail penjelasan Anda, semakin cepat kami dapat membantu')
                    ->columnSpanFull(),

                Forms\Components\Select::make('priority')
                    ->label('Prioritas')
                    ->options(Ticket::PRIORITY_OPTIONS)
                    ->default('medium')
                    ->required()
                    ->native(false)
                    ->helperText('Pilih tingkat urgensi masalah Anda'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_tiket')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('permohonan.kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable()
                    ->sortable()
                    ->default('Pertanyaan Umum')
                    ->description(fn (Ticket $record): string => 
                        $record->permohonan 
                            ? 'Terkait permohonan spesifik' 
                            : ($record->layanan ? 'Tentang: ' . $record->layanan->name : 'Umum')
                    ),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => Ticket::STATUS_OPTIONS[$state] ?? $state)
                    ->colors([
                        'secondary' => 'open',
                        'primary' => 'in_progress',
                        'success' => 'resolved',
                        'gray' => 'closed',
                    ]),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Prioritas')
                    ->formatStateUsing(fn (string $state): string => Ticket::PRIORITY_OPTIONS[$state] ?? $state)
                    ->colors([
                        'secondary' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'danger' => 'urgent',
                    ]),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Ditangani Oleh')
                    ->default('Belum ditugaskan')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Ticket::STATUS_OPTIONS)
                    ->native(false),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Ticket::PRIORITY_OPTIONS)
                    ->native(false),
                Tables\Filters\SelectFilter::make('layanan_id')
                    ->label('Jenis Layanan')
                    ->options(function () {
                        return Layanan::where('is_active', true)
                            ->with('kategoriLayanan')
                            ->get()
                            ->mapWithKeys(function ($layanan) {
                                return [$layanan->id => $layanan->kategoriLayanan->name . ' - ' . $layanan->name];
                            });
                    })
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Tiket')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_tiket')->label('Kode Tiket'),
                        TextEntry::make('permohonan.kode_permohonan')
                            ->label('Kode Permohonan')
                            ->default('Pertanyaan Umum'),
                        TextEntry::make('layanan.name')
                            ->label('Jenis Layanan')
                            ->default('Umum'),
                        TextEntry::make('subject')->label('Judul'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'open' => 'gray',
                                'in_progress' => 'info',
                                'resolved' => 'success',
                                'closed' => 'secondary',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => Ticket::STATUS_OPTIONS[$state] ?? $state),
                        TextEntry::make('priority')
                            ->label('Prioritas')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'low' => 'gray',
                                'medium' => 'warning',
                                'high' => 'danger',
                                'urgent' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => Ticket::PRIORITY_OPTIONS[$state] ?? $state),
                        TextEntry::make('assignedTo.name')->label('Ditangani Oleh')->default('Belum ditugaskan'),
                        TextEntry::make('created_at')->label('Tanggal Dibuat')->dateTime(),
                        TextEntry::make('resolved_at')->label('Tanggal Selesai')->dateTime(),
                    ]),

                InfolistSection::make('Deskripsi Masalah')
                    ->schema([
                        TextEntry::make('description')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record:kode_tiket}'),
        ];
    }
}