<?php

namespace App\Filament\Petugas\Resources\UserResource\Pages;

use App\Filament\Petugas\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use App\Models\User;

// Menggunakan Infolist dan komponen-komponennya
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    // Menggunakan method infolist() yang benar, bukan form()
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Menggunakan Grid dari Infolist
            Grid::make(2)->schema([
                // Menggunakan Section dari Infolist
                Section::make('Informasi Identitas')
                    ->schema([
                        // Menggunakan TextEntry untuk menampilkan data
                        TextEntry::make('name')->label('Nama Lengkap'),
                        TextEntry::make('email')->label('Alamat Email'),
                        TextEntry::make('nik')->label('NIK'),
                        TextEntry::make('nomor_kk')->label('Nomor Kartu Keluarga'),
                        TextEntry::make('jenis_kelamin')->label('Jenis Kelamin'),
                        TextEntry::make('agama')->label('Agama'),
                    ])->columns(2),
                
                Section::make('Data Kelahiran')
                    ->schema([
                        TextEntry::make('tempat_lahir')->label('Tempat Lahir'),
                        TextEntry::make('tanggal_lahir')->label('Tanggal Lahir')->date(),
                        TextEntry::make('gol_darah')->label('Golongan Darah'),
                    ])->columns(3),

                Section::make('Alamat & Kontak')
                    ->schema([
                        TextEntry::make('alamat')->label('Alamat Lengkap')->columnSpanFull(),
                        TextEntry::make('rt_rw')->label('RT/RW'),
                        TextEntry::make('desa_kelurahan')->label('Desa/Kelurahan'),
                        TextEntry::make('kecamatan')->label('Kecamatan'),
                        TextEntry::make('kabupaten')->label('Kabupaten'),
                        TextEntry::make('nomor_whatsapp')->label('Nomor Whatsapp')->columnSpan(2),
                    ])->columns(3),
                
                Section::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('status_keluarga')->label('Status dalam Keluarga'),
                        TextEntry::make('status_perkawinan')->label('Status Perkawinan'),
                        TextEntry::make('pekerjaan')->label('Pekerjaan'),
                        TextEntry::make('pendidikan')->label('Pendidikan Terakhir'),
                    ])->columns(2),

                Section::make('Dokumen Terunggah')
                    ->schema(function (User $record): array {
                        $fields = [];
                        $documentFields = [
                            'foto_ktp' => 'Foto KTP',
                            'foto_kk' => 'Foto Kartu Keluarga',
                            'foto_tanda_tangan' => 'Foto Tanda Tangan',
                            'foto_selfie_ktp' => 'Foto Selfie dengan KTP',
                        ];

                        foreach ($documentFields as $field => $label) {
                            $filePath = $record->{$field};
                            $entry = TextEntry::make($field)->label($label);

                            if ($filePath) {
                                $entry->state('Unduh Dokumen')
                                      ->color('primary')
                                      ->url(route('secure.download.profile', ['user_id' => $record->id, 'field' => $field]), true)
                                      ->icon('heroicon-m-arrow-down-tray');
                            } else {
                                $entry->state('Belum diunggah')
                                      ->color('danger')
                                      ->icon('heroicon-o-x-circle');
                            }
                            $fields[] = $entry;
                        }
                        return $fields;
                    })->columns(2),
            ]),
        ]);
    }
}