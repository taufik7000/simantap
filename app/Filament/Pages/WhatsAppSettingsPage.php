<?php

namespace App\Filament\Pages;

use App\Models\WhatsAppSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WhatsAppSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Manajemen Sistem';
    protected static ?string $navigationLabel = 'Pengaturan WhatsApp';
    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.whats-app-settings-page'; // <-- Path view juga disesuaikan

    public ?array $data = [];

    public function mount(): void
    {
        $settings = WhatsAppSetting::first();
        if ($settings) {
            $this->form->fill($settings->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status Verifikasi')
                    ->description('Aktifkan atau nonaktifkan verifikasi nomor WhatsApp untuk pendaftar baru.')
                    ->schema([
                        Toggle::make('verification_enabled')
                            ->label('Aktifkan Verifikasi WhatsApp')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('Kredensial Wajib API WhatsApp')
                    ->schema([
                        TextInput::make('phone_number_id')->label('ID Nomor Telepon')->required(),
                        Textarea::make('access_token')->label('Token Akses Permanen')->required()->rows(4),
                    ]),

                Section::make('Template Pesan')
                    ->schema([
                        TextInput::make('otp_template_name')->label('Nama Template Pesan OTP')->required(),
                        TextInput::make('status_template_name')->label('Nama Template Notifikasi Status')->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Simpan Perubahan')->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            WhatsAppSetting::updateOrCreate(['id' => 1], $data);
            Notification::make()->title('Pengaturan berhasil disimpan')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Gagal menyimpan pengaturan')->body($e->getMessage())->danger()->send();
        }
    }
}