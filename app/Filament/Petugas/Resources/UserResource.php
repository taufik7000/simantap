<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ViewColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Kependudukan';
    protected static ?string $navigationLabel = 'Data Penduduk';
    protected static ?string $modelLabel = 'Warga';
    protected static ?string $pluralModelLabel = 'Data Penduduk';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('nik')->label('NIK')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                ViewColumn::make('status')
                    ->label('Status Akun')
                    ->view('filament.tables.columns.status-akun-badge'),                
                TextColumn::make('created_at')->label('Tanggal Daftar')->dateTime('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat Data'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}