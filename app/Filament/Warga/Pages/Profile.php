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
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.warga.pages.profile';
    protected static ?string $title = 'Profil Saya';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(auth()->user()->attributesToArray());
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
                                Section::make('Data Diri')
                                    ->schema([
                                        TextInput::make('nik')->label('NIK')->disabled(fn (): bool => $this->getFormModel()->verified_at !== null)->helperText(fn (): ?string => $this->getFormModel()->verified_at !== null ? 'NIK tidak bisa diubah setelah akun terverifikasi.' : null),
                                        TextInput::make('name')->label('Nama Lengkap')->required(),
                                        TextInput::make('email')->label('Alamat Email')->email()->required(),
                                        TextInput::make('nomor_kk')->label('Nomor Kartu Keluarga')->required(),
                                        TextInput::make('nomor_whatsapp')->label('Nomor Whatsapp')->required(),
                                        Textarea::make('alamat')->label('Alamat Lengkap')->rows(3)->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Dokumen Pendukung')
                                    ->description('Unggah dokumen Anda di sini. Pastikan gambar jelas dan tidak buram.')
                                    ->schema([
                                        FileUpload::make('foto_ktp')->label('Foto KTP')->directory('dokumen_warga')->visibility('public'),
                                        FileUpload::make('foto_kk')->label('Foto Kartu Keluarga')->directory('dokumen_warga')->visibility('public'),
                                        FileUpload::make('foto_tanda_tangan')->label('Foto Tanda Tangan')->directory('dokumen_warga')->visibility('public'),
                                        FileUpload::make('foto_selfie_ktp')->label('Foto Selfie dengan KTP')->directory('dokumen_warga')->visibility('public'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Ubah Password')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('password')->password()->label('Password Baru')->helperText('Biarkan kosong jika tidak ingin mengubah password.')->confirmed(),
                                TextInput::make('password_confirmation')->password()->label('Konfirmasi Password Baru'),
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

    // 1. Siapkan data non-file dan NIK untuk diupdate
    $updateData = [
        'name' => $data['name'],
        'email' => $data['email'],
        'nomor_kk' => $data['nomor_kk'],
        'nomor_whatsapp' => $data['nomor_whatsapp'],
        'alamat' => $data['alamat'],
    ];

    if (!$user->verified_at) {
        $updateData['nik'] = $data['nik'];
    }

    // 2. Logika perbaikan untuk menangani file upload
    $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
    
    foreach ($fileFields as $field) {
        if (isset($data[$field])) {
            if (is_array($data[$field])) {
                if (empty($data[$field])) {
                    // File dihapus oleh pengguna
                    if ($user->{$field}) {
                        Storage::disk('public')->delete($user->{$field});
                    }
                    $updateData[$field] = null;
                } else {
                    // File baru diunggah
                    $newPath = is_array($data[$field]) ? $data[$field][0] : $data[$field];
                    
                    // Hapus file lama jika ada dan berbeda dari yang baru
                    if ($user->{$field} && $user->{$field} !== $newPath) {
                        Storage::disk('public')->delete($user->{$field});
                    }
                    $updateData[$field] = $newPath;
                }
            } elseif (is_string($data[$field])) {
                // File sudah ada, tidak ada perubahan atau path langsung
                $updateData[$field] = $data[$field];
            }
        }
    }
    
    // 3. Logika untuk password
    if (!empty($data['password'])) {
        $updateData['password'] = Hash::make($data['password']);
    }

    // 4. Update pengguna
    $user->update($updateData);

    Notification::make()
        ->title('Profil berhasil diperbarui')
        ->success()
        ->send();
        
    // 5. Refresh form dengan data terbaru
    $this->form->fill($user->fresh()->attributesToArray());

    if (!empty($data['password'])) {
        $this->redirect(static::getUrl());
    }
}
}