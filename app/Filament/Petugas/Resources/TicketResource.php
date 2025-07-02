<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationGroup = 'Manajemen Pelayanan';
    protected static ?string $navigationLabel = 'Tiket Support';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Tiket')
                            ->schema([
                                Forms\Components\TextInput::make('kode_tiket')
                                    ->label('Kode Tiket')
                                    ->disabled(),

                                Forms\Components\TextInput::make('user.name')
                                    ->label('Diajukan Oleh')
                                    ->disabled(),

                                Forms\Components\TextInput::make('permohonan.kode_permohonan')
                                    ->label('Kode Permohonan')
                                    ->disabled(),

                                Forms\Components\TextInput::make('subject')
                                    ->label('Judul')
                                    ->disabled(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->disabled()
                                    ->rows(3),
                            ])->columns(2),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Manajemen Tiket')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options(Ticket::STATUS_OPTIONS)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state === 'resolved') {
                                            $set('resolved_at', now());
                                        } elseif ($state !== 'resolved') {
                                            $set('resolved_at', null);
                                        }
                                    }),

                                Forms\Components\Select::make('priority')
                                    ->label('Prioritas')
                                    ->options(Ticket::PRIORITY_OPTIONS)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('assigned_to')
                                    ->label('Tugaskan Ke')
                                    ->options(function () {
                                        return User::role(['petugas', 'admin'])
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih petugas'),

                                Forms\Components\DateTimePicker::make('resolved_at')
                                    ->label('Tanggal Diselesaikan')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Textarea::make('internal_notes')
                                    ->label('Catatan Internal')
                                    ->placeholder('Catatan untuk tim internal...')
                                    ->rows(3),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permohonan.kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
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
                    ->label('Ditugaskan Ke')
                    ->default('Belum ditugaskan')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Pesan')
                    ->counts('messages')
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Ticket::STATUS_OPTIONS)
                    ->native(false),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Ticket::PRIORITY_OPTIONS)
                    ->native(false),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->options(function () {
                        return User::role(['petugas', 'admin'])
                            ->pluck('name', 'id');
                    })
                    ->label('Ditugaskan Ke')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('quick_assign')
                    ->label('Ambil Tiket')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->action(function (Ticket $record) {
                        $record->update([
                            'assigned_to' => auth()->id(),
                            'status' => $record->status === 'open' ? 'in_progress' : $record->status,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Tiket berhasil diambil')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Ticket $record) => !$record->assigned_to),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('assign_to_me')
                    ->label('Tugaskan ke Saya')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each(function (Ticket $record) {
                            $record->update([
                                'assigned_to' => auth()->id(),
                                'status' => $record->status === 'open' ? 'in_progress' : $record->status,
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil mengambil ' . $records->count() . ' tiket')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\BulkAction::make('mark_resolved')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each(function (Ticket $record) {
                            $record->update([
                                'status' => 'resolved',
                                'resolved_at' => now(),
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil menyelesaikan ' . $records->count() . ' tiket')
                            ->success()
                            ->send();
                    }),
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
                        TextEntry::make('user.name')->label('Pemohon'),
                        TextEntry::make('permohonan.kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('subject')->label('Judul')->columnSpanFull(),
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

                InfolistSection::make('Informasi Pemohon')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.email')->label('Email'),
                        TextEntry::make('user.nomor_whatsapp')->label('WhatsApp'),
                        TextEntry::make('user.nik')->label('NIK'),
                        TextEntry::make('user.alamat')->label('Alamat')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'view' => Pages\ViewTicket::route('/{record:kode_tiket}'),
            'edit' => Pages\EditTicket::route('/{record:kode_tiket}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['open', 'in_progress'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getNavigationBadge();
        return $count > 0 ? 'warning' : null;
    }
}