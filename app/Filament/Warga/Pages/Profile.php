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
                                        TextInput::make('nik')->label('NIK')->disabled(fn (): bool => $this->getFormModel()->verified_at !== null)->helperText(fn (): ?string => $this->getFormModel()->verified_at !== null ? 'NIK tidak bisa diubah setelah akun terverifikasi.' : null),
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
                                        FileUpload::make('foto_ktp')->label('Foto KTP')->directory('dokumen_warga')->visibility('public')->previewable(false),
                                        FileUpload::make('foto_kk')->label('Foto Kartu Keluarga')->directory('dokumen_warga')->visibility('public')->previewable(false),
                                        FileUpload::make('foto_tanda_tangan')->label('Foto Tanda Tangan')->directory('dokumen_warga')->visibility('public')->previewable(false),
                                        FileUpload::make('foto_selfie_ktp')->label('Foto Selfie dengan KTP')->directory('dokumen_warga')->visibility('public')->previewable(false),
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

        // Siapkan data non-file dan NIK untuk diupdate
        $updateData = [
            'name' => $data['name'], 'email' => $data['email'], 'nomor_kk' => $data['nomor_kk'],
            'nomor_whatsapp' => $data['nomor_whatsapp'], 'alamat' => $data['alamat'],
            'jenis_kelamin' => $data['jenis_kelamin'], 'agama' => $data['agama'],
            'tempat_lahir' => $data['tempat_lahir'], 'tanggal_lahir' => $data['tanggal_lahir'], 'gol_darah' => $data['gol_darah'],
            'rt_rw' => $data['rt_rw'], 'desa_kelurahan' => $data['desa_kelurahan'],
            'kecamatan' => $data['kecamatan'], 'kabupaten' => $data['kabupaten'],
            'status_keluarga' => $data['status_keluarga'], 'status_perkawinan' => $data['status_perkawinan'],
            'pekerjaan' => $data['pekerjaan'], 'pendidikan' => $data['pendidikan'],
        ];

        if (!$user->verified_at) {
            $updateData['nik'] = $data['nik'];
        }

        // Logika file upload tetap sama
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        foreach ($fileFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                if (empty($data[$field])) {
                    if ($user->{$field}) { Storage::disk('public')->delete($user->{$field}); }
                    $updateData[$field] = null;
                } else {
                    $newPath = head($data[$field]);
                    if ($user->{$field} && $user->{$field} !== $newPath) { Storage::disk('public')->delete($user->{$field}); }
                    $updateData[$field] = $newPath;
                }
            }
        }
        
        // Logika untuk password - hanya update jika ada input password
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        Notification::make()->title('Profil berhasil diperbarui')->success()->send();

        // Reset password fields setelah update berhasil
        $this->data['password'] = '';
        $this->data['password_confirmation'] = '';
        
        // Refresh form state
        $this->form->fill($this->data);

        // Jika password diubah, lakukan redirect agar lebih aman
        if (!empty($data['password'])) {
            $this->redirect(static::getUrl());
        }
    }
}