<?php

namespace App\Filament\Petugas\Resources\UserResource\Pages;

use App\Filament\Petugas\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Section::make('Data Pribadi')
                    ->schema([
                        TextInput::make('name')->label('Nama Lengkap'),
                        TextInput::make('nik')->label('NIK'),
                        TextInput::make('nomor_kk')->label('Nomor KK'),
                        TextInput::make('email')->label('Email'),
                        TextInput::make('nomor_whatsapp')->label('No. Whatsapp'),
                        Textarea::make('alamat')->label('Alamat')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                
                Section::make('Dokumen Terunggah')
                    ->schema([
                        ViewField::make('foto_ktp')->view('filament.fields.image-viewer'),
                        ViewField::make('foto_kk')->view('filament.fields.image-viewer'),
                        ViewField::make('foto_tanda_tangan')->view('filament.fields.image-viewer'),
                        ViewField::make('foto_selfie_ktp')->view('filament.fields.image-viewer'),
                    ])->columns(2),
            ]),
        ])->disabled();
    }
}