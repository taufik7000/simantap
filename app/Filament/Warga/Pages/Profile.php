<?php

namespace App\Filament\Warga\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Kelurahan;
use Illuminate\Support\Arr;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.warga.pages.profile';
    protected static ?string $navigationGroup = 'Akun Saya';
    protected static ?string $title = 'Profil Saya';

    public ?array $data = [];

    public function mount(): void
    {
        $userData = auth()->user()->attributesToArray();

        // Hapus password dari data yang akan diisi ke form
        unset($userData['password']);

        $this->form->fill($userData);
    }

    protected function getFormModel(): Model
    {
        return auth()->user();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Data & Dokumen')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Section::make('Informasi Identitas')
                                    ->schema([
                                        TextInput::make('name')->label('Nama Lengkap')->required(),
                                        TextInput::make('email')->label('Alamat Email')->email()->required(),
                                        TextInput::make('nik')->label('NIK')->disabled(fn(): bool => $this->getFormModel()->verified_at !== null)->helperText(fn(): ?string => $this->getFormModel()->verified_at !== null ? 'NIK tidak bisa diubah setelah akun terverifikasi.' : null),
                                        TextInput::make('nomor_kk')->label('Nomor Kartu Keluarga'),
                                        Select::make('jenis_kelamin')->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']),
                                        Select::make('agama')->options(['Islam' => 'Islam', 'Kristen Protestan' => 'Kristen Protestan', 'Kristen Katolik' => 'Kristen Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Khonghucu' => 'Khonghucu']),
                                    ])->columns(2),

                                Section::make('Data Kelahiran')
                                    ->schema([
                                        TextInput::make('tempat_lahir'),
                                        DatePicker::make('tanggal_lahir'),
                                        Select::make('gol_darah')->options(['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O', 'Tidak Tahu' => 'Tidak Tahu']),
                                    ])->columns(3),

                                Section::make('Alamat & Kontak')
                                    ->schema([
                                        Textarea::make('alamat')->label('Alamat Lengkap')->rows(2)->columnSpanFull(),
                                        TextInput::make('rt_rw')->label('RT/RW')->placeholder('001/001'),
                                        TextInput::make('desa_kelurahan')->label('Desa/Kelurahan'),
                                        TextInput::make('kecamatan')->label('Kecamatan'),
                                        TextInput::make('kabupaten')->label('Kabupaten'),
                                        TextInput::make('nomor_whatsapp')->label('Nomor Whatsapp')->required()->columnSpan(2),
                                    ])->columns(3),

                                Section::make('Informasi Tambahan')
                                    ->schema([
                                        Select::make('status_keluarga')->options(['Kepala Keluarga' => 'Kepala Keluarga', 'Anggota Keluarga' => 'Anggota Keluarga']),
                                        Select::make('status_perkawinan')->options(['Belum Kawin' => 'Belum Kawin', 'Kawin' => 'Kawin', 'Cerai Hidup' => 'Cerai Hidup', 'Cerai Mati' => 'Cerai Mati']),
                                        TextInput::make('pekerjaan'),
                                        Select::make('pendidikan')->options(['Tidak Sekolah' => 'Tidak Sekolah', 'SD' => 'SD', 'SMP' => 'SMP', 'SMA/SMK' => 'SMA/SMK', 'Diploma' => 'Diploma', 'S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3']),
                                    ])->columns(2),

                                Section::make('Dokumen Pendukung')
                                    ->schema([
                                        FileUpload::make('foto_ktp')->label('Foto KTP')
                                            ->disk('private')
                                            ->downloadable()
                                            ->previewable(false),

                                        FileUpload::make('foto_kk')->label('Foto Kartu Keluarga')
                                            ->disk('private')
                                            ->downloadable()
                                            ->previewable(false),

                                        FileUpload::make('foto_tanda_tangan')->label('Foto Tanda Tangan')
                                            ->disk('private')
                                            ->downloadable()
                                            ->previewable(false),

                                        FileUpload::make('foto_selfie_ktp')->label('Foto Selfie dengan KTP')
                                            ->disk('private')
                                            ->downloadable()
                                            ->previewable(false),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Akun & Keamanan')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->label('Password Baru')
                                    ->helperText('Biarkan kosong jika tidak ingin mengubah password.')
                                    ->confirmed()
                                    ->dehydrated(false), // Ini penting: field tidak akan di-hydrate dari model
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->label('Konfirmasi Password Baru')
                                    ->dehydrated(false), // Ini juga tidak di-hydrate dari model
                            ])->columns(2),
                    ])
                    ->columnSpanFull()
            ])
            ->model($this->getFormModel())
            ->statePath('data');
    }

    public function updateProfile(): void
    {
        $user = $this->getFormModel();
        $data = $this->form->getState();

        // Pisahkan data file dari data non-file
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        $nonFileUpdateData = Arr::except($data, $fileFields);

        // Logika untuk update password - hanya update jika ada input password baru
        if (!empty($nonFileUpdateData['password'])) {
            $nonFileUpdateData['password'] = Hash::make($nonFileUpdateData['password']);
        } else {
            // Hapus password dari data update jika kosong
            unset($nonFileUpdateData['password']);
            unset($nonFileUpdateData['password_confirmation']);
        }

        // Update data non-file terlebih dahulu
        $user->update($nonFileUpdateData);

        // ===============================================
        // AWAL BLOK LOGIKA FILE UPLOAD YANG DIPERBAIKI
        // ===============================================
        foreach ($fileFields as $field) {
            // Cek apakah ada data baru untuk field file ini
            if (isset($data[$field])) {
                $newPath = $data[$field];
                $oldPath = $user->getOriginal($field);

                // Jika path baru berbeda dengan yang lama
                if ($newPath !== $oldPath) {
                    // Hapus file lama jika ada
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    // Simpan path baru (bisa berupa path string atau null jika file dihapus)
                    $user->{$field} = $newPath;
                }
            }
        }
        // Simpan perubahan file ke database
        $user->save();
        // ===============================================
        // AKHIR BLOK LOGIKA FILE UPLOAD
        // ===============================================

        Notification::make()->title('Profil berhasil diperbarui')->success()->send();

        // Jika password diubah, lakukan redirect agar lebih aman
        if (!empty($data['password'])) {
            $this->redirect(static::getUrl());
        } else {
            // Refresh form state untuk menampilkan data terbaru
            $this->form->fill($user->fresh()->toArray());
        }
    }
}