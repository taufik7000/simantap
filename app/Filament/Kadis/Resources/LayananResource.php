<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\LayananResource\Pages;
use App\Models\FormulirMaster;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard; // Import Wizard
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Semua Layanan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Manajemen Layanan';
    protected static ?string $pluralModelLabel = 'Layanan Tersedia';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section untuk informasi dasar layanan tetap sama
                Section::make('Informasi Dasar Layanan')
                    ->description('Masukkan detail dasar untuk kategori layanan ini.')
                    ->schema([
                        Select::make('kategori_layanan_id')
                            ->relationship('KategoriLayanan', 'name')
                            ->label('Kategori Layanan Induk')
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Layanan')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('Aktifkan Layanan Ini?')
                            ->default(true),
                    ])->columns(2),

                // --- PERBAIKAN UTAMA: Mengganti Repeater dengan Wizard (Tabs Dinamis) ---
                Repeater::make('description')
                    ->label('Jenis-Jenis Permohonan')
                    ->addActionLabel('Tambah Jenis Permohonan Baru')
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['nama_syarat'] ?? 'Jenis Permohonan Baru')
                    ->schema([
                        // Setiap item repeater akan menjadi sebuah "tab"
                        Wizard::make([
                            Wizard\Step::make('Informasi Dasar')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                    TextInput::make('nama_syarat')
                                        ->label('Nama Jenis Permohonan')
                                        ->helperText('Contoh: Pembuatan KTP Baru, Perpanjangan SIM A')
                                        ->required()
                                        ->columnSpanFull(),
                                    RichEditor::make('deskripsi_syarat')
                                        ->label('Deskripsi & Keterangan untuk Warga')
                                        ->toolbarButtons([
                                            'bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'link',
                                        ])
                                        ->required(),
                                    Select::make('formulir_master_id')
                                        ->label('Lampirkan Formulir Master (PDF)')
                                        ->options(FormulirMaster::all()->pluck('nama_formulir', 'id'))
                                        ->multiple()
                                        ->searchable()
                                        ->placeholder('Kosongkan jika tidak ada formulir PDF'),
                                ]),

                            Wizard\Step::make('Form Builder')
                                ->icon('heroicon-o-pencil-square')
                                ->description('Buat field formulir untuk diisi oleh warga.')
                                ->schema([
                                    Repeater::make('form_fields')
                                        ->label(false) // Label sudah ada di Step
                                        ->schema(self::getFormBuilderFields()) // Menggunakan fungsi terpisah agar rapi
                                        ->addActionLabel('Tambah Field')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['field_label'] ?? 'Field Baru'),
                                ]),

                            Wizard\Step::make('Syarat Berkas')
                                ->icon('heroicon-o-document-arrow-up')
                                ->description('Tentukan berkas yang wajib di-upload oleh warga.')
                                ->schema([
                                    Repeater::make('file_requirements')
                                        ->label(false) // Label sudah ada di Step
                                        ->schema(self::getFileRequirementFields()) // Menggunakan fungsi terpisah
                                        ->addActionLabel('Tambah Syarat Berkas')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['file_name'] ?? 'Berkas Baru'),
                                ]),
                        ])->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Helper function untuk mendefinisikan field pada Form Builder.
     * Menggunakan Tailwind CSS classes via ->extraAttributes() untuk tampilan.
     */
    protected static function getFormBuilderFields(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('field_label')
                    ->label('Label Field (Untuk Warga)')
                    ->required()
                    ->placeholder('Contoh: Alamat Lengkap'),
                TextInput::make('field_name')
                    ->label('Nama Field (Untuk Sistem)')
                    ->required()
                    ->placeholder('Contoh: alamat_lengkap')
                    ->helperText('Gunakan huruf kecil dan underscore, tanpa spasi.'),
            ]),
            Grid::make(3)->schema([
                Select::make('field_type')
                    ->label('Tipe Field')
                    ->options([
                        'text' => 'Teks Pendek', 'textarea' => 'Teks Panjang',
                        'select' => 'Pilihan Dropdown', 'checkbox' => 'Checkbox',
                        'radio' => 'Radio Button', 'date' => 'Tanggal',
                        'number' => 'Angka', 'email' => 'Email',
                    ])
                    ->reactive()->required(),
                Toggle::make('is_required')->label('Wajib Diisi')->default(true),
                TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            ]),
            // Opsi untuk select/radio/checkbox
            Repeater::make('field_options')
                ->label('Opsi Pilihan')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('value')->label('Nilai (Sistem)')->required(),
                        TextInput::make('label')->label('Label (Warga)')->required(),
                    ])
                ])
                ->visible(fn ($get) => in_array($get('field_type'), ['select', 'radio', 'checkbox']))
                ->addActionLabel('Tambah Opsi')
                ->collapsible()->collapsed(),
        ];
    }

    /**
     * Helper function untuk mendefinisikan persyaratan berkas.
     */
    protected static function getFileRequirementFields(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('file_name')->label('Nama Berkas')->required()->placeholder('Contoh: Scan KTP'),
                TextInput::make('file_key')->label('Key (Sistem)')->required()->placeholder('Contoh: file_ktp'),
            ]),
            Grid::make(3)->schema([
                Select::make('file_type')->label('Tipe File')->options([
                    'image' => 'Gambar (JPG, PNG)', 'pdf' => 'PDF',
                    'document' => 'Dokumen (PDF, DOC, DOCX)', 'any' => 'Semua Tipe'
                ])->default('pdf'),
                Toggle::make('is_required')->label('Wajib')->default(true),
                TextInput::make('max_size')->label('Ukuran Max (MB)')->numeric()->default(2),
            ]),
            Textarea::make('file_description')->label('Keterangan Tambahan')->placeholder('Petunjuk untuk warga...'),
        ];
    }
    
    // Method table() dan lainnya tetap sama
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Sub Layanan')->searchable(),
                Tables\Columns\TextColumn::make('kategorilayanan.name')->label('Kategori Induk')->searchable()->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }
}