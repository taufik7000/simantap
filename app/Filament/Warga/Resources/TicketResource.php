<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\Permohonan;
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
                Forms\Components\Select::make('permohonan_id')
                    ->label('Permohonan Terkait')
                    ->options(function () {
                        return Permohonan::where('user_id', Auth::id())
                            ->with('layanan')
                            ->get()
                            ->mapWithKeys(function ($permohonan) {
                                return [$permohonan->id => $permohonan->kode_permohonan . ' - ' . $permohonan->layanan->name];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn () => request()->query('permohonan_id')),

                Forms\Components\TextInput::make('subject')
                    ->label('Judul Tiket')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ringkas masalah Anda dalam satu kalimat'),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Masalah')
                    ->required()
                    ->rows(5)
                    ->placeholder('Jelaskan masalah atau pertanyaan Anda secara detail...'),

                Forms\Components\Select::make('priority')
                    ->label('Prioritas')
                    ->options(Ticket::PRIORITY_OPTIONS)
                    ->default('medium')
                    ->required()
                    ->native(false),
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
                    ->sortable(),

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
                        TextEntry::make('permohonan.kode_permohonan')->label('Kode Permohonan'),
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