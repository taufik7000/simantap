<?php

namespace App\Filament\Petugas\Resources\UserResource\Pages;

use App\Filament\Petugas\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Section::make('Informasi Identitas')
                    ->schema([
                        TextInput::make('name')->label('Nama Lengkap'),
                        TextInput::make('email')->label('Alamat Email'),
                        TextInput::make('nik')->label('NIK'),
                        TextInput::make('nomor_kk')->label('Nomor Kartu Keluarga'),
                        TextInput::make('jenis_kelamin')->label('Jenis Kelamin'),
                        TextInput::make('agama')->label('Agama'),
                    ])->columns(2),
                
                Section::make('Data Kelahiran')
                    ->schema([
                        TextInput::make('tempat_lahir')->label('Tempat Lahir'),
                        TextInput::make('tanggal_lahir')->label('Tanggal Lahir'),
                        TextInput::make('gol_darah')->label('Golongan Darah'),
                    ])->columns(3),

                Section::make('Alamat & Kontak')
                    ->schema([
                        Textarea::make('alamat')->label('Alamat Lengkap')->rows(2)->columnSpanFull(),
                        TextInput::make('rt_rw')->label('RT/RW'),
                        TextInput::make('desa_kelurahan')->label('Desa/Kelurahan'),
                        TextInput::make('kecamatan')->label('Kecamatan'),
                        TextInput::make('kabupaten')->label('Kabupaten'),
                        TextInput::make('nomor_whatsapp')->label('Nomor Whatsapp')->columnSpan(2),
                    ])->columns(3),
                
                Section::make('Informasi Tambahan')
                    ->schema([
                        TextInput::make('status_keluarga')->label('Status dalam Keluarga'),
                        TextInput::make('status_perkawinan')->label('Status Perkawinan'),
                        TextInput::make('pekerjaan')->label('Pekerjaan'),
                        TextInput::make('pendidikan')->label('Pendidikan Terakhir'),
                    ])->columns(2),

                Section::make('Dokumen Terunggah')
                    ->schema([
                        FileUpload::make('foto_ktp')->label('Foto KTP')->image(),
                        FileUpload::make('foto_kk')->label('Foto Kartu Keluarga')->image(),
                        FileUpload::make('foto_tanda_tangan')->label('Foto Tanda Tangan')->image(),
                        FileUpload::make('foto_selfie_ktp')->label('Foto Selfie dengan KTP')->image(),
                    ])->columns(2),
            ]),
        ])->disabled();
    }
}