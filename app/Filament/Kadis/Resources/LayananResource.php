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
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Select::make('kategori_layanan_id')
                            ->relationship('KategoriLayanan', 'name')
                            ->label('Pilih Kategori Layanan Induk')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sub Layanan')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Layanan ini Aktif?')
                            ->default(true),
                    ])->columns(2),

                // START: Form Builder Integration
                Repeater::make('description')
                    ->label('Persyaratan Layanan')
                    ->schema([
                        TextInput::make('nama_syarat')
                            ->label('Nama Jenis Permohonan')
                            ->required(),

                        RichEditor::make('deskripsi_syarat')
                            ->label('Deskripsi & Syarat Lengkap')
                            ->required(),

                        // EXISTING: Formulir Master PDF
                        Select::make('formulir_master_id')
                            ->label('Pilih Formulir Master (PDF)')
                            ->options(FormulirMaster::all()->pluck('nama_formulir', 'id'))
                            ->searchable()
                            ->multiple()
                            ->placeholder('Tidak ada formulir master'),

                        // NEW: Form Builder Dinamis
                        Section::make('Form Builder - Data yang Dikumpulkan')
                            ->description('Buat form khusus untuk mengumpulkan data dari warga')
                            ->schema([
                                Repeater::make('form_fields')
                                    ->label('Field Formulir')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('field_name')
                                                ->label('Nama Field (Internal)')
                                                ->required()
                                                ->helperText('Contoh: alamat_baru, nomor_telepon'),

                                            TextInput::make('field_label')
                                                ->label('Label Field (User)')
                                                ->required()
                                                ->helperText('Contoh: Alamat Lengkap Baru'),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('field_type')
                                                ->label('Tipe Field')
                                                ->options([
                                                    'text' => 'Teks Pendek',
                                                    'textarea' => 'Teks Panjang',
                                                    'select' => 'Pilihan Dropdown',
                                                    'checkbox' => 'Checkbox',
                                                    'radio' => 'Radio Button',
                                                    'date' => 'Tanggal',
                                                    'number' => 'Angka',
                                                    'email' => 'Email',
                                                ])
                                                ->reactive()
                                                ->required(),

                                            Toggle::make('is_required')
                                                ->label('Wajib Diisi')
                                                ->default(false),

                                            TextInput::make('sort_order')
                                                ->label('Urutan')
                                                ->numeric()
                                                ->default(0),
                                        ]),

                                        // Opsi untuk select/radio/checkbox
                                        Repeater::make('field_options')
                                            ->label('Pilihan Opsi')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextInput::make('value')->label('Nilai')->required(),
                                                    TextInput::make('label')->label('Label')->required(),
                                                ])
                                            ])
                                            ->visible(fn ($get) => in_array($get('field_type'), ['select', 'radio', 'checkbox']))
                                            ->collapsible(),

                                        Textarea::make('help_text')
                                            ->label('Teks Bantuan')
                                            ->placeholder('Petunjuk untuk warga...'),

                                        TagsInput::make('validation_rules')
                                            ->label('Aturan Validasi')
                                            ->suggestions([
                                                'required' => 'Wajib diisi',
                                                'min:3' => 'Minimal 3 karakter',
                                                'max:255' => 'Maksimal 255 karakter',
                                                'numeric' => 'Hanya angka',
                                                'email' => 'Format email valid'
                                            ])
                                    ])
                                    ->addActionLabel('Tambah Field Form')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(
                                        fn (array $state): ?string =>
                                        ($state['field_label'] ?? 'Field Baru') .
                                            ' (' . ($state['field_type'] ?? 'text') . ')'
                                    )
                            ])
                            ->collapsible()
                            ->collapsed(),

                        // NEW: File Requirements Builder
                        Section::make('File Requirements - Berkas yang Wajib Diupload')
                            ->description('Tentukan berkas apa saja yang harus diupload warga')
                            ->schema([
                                Repeater::make('file_requirements')
                                    ->label('Persyaratan Berkas')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('file_name')
                                                ->label('Nama Berkas')
                                                ->required()
                                                ->helperText('Contoh: KTP Lama, Kartu Keluarga'),

                                            TextInput::make('file_key')
                                                ->label('Key (Internal)')
                                                ->required()
                                                ->helperText('Contoh: ktp_lama, kartu_keluarga'),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('file_type')
                                                ->label('Tipe File')
                                                ->options([
                                                    'image' => 'Gambar (JPG, PNG)',
                                                    'pdf' => 'PDF',
                                                    'document' => 'Dokumen (PDF, DOC, DOCX)',
                                                    'any' => 'Semua Tipe'
                                                ])
                                                ->default('image'),

                                            Toggle::make('is_required')
                                                ->label('Wajib')
                                                ->default(true),

                                            TextInput::make('max_size')
                                                ->label('Ukuran Max (MB)')
                                                ->numeric()
                                                ->default(2),
                                        ]),

                                        Textarea::make('file_description')
                                            ->label('Keterangan')
                                            ->placeholder('Petunjuk untuk warga tentang berkas ini...')
                                    ])
                                    ->addActionLabel('Tambah Persyaratan Berkas')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(
                                        fn (array $state): ?string =>
                                        $state['file_name'] ?? 'Berkas Baru'
                                    )
                            ])
                            ->collapsible()
                            ->collapsed()
                    ])
                    ->addActionLabel('Tambah Jenis Permohonan')
                    ->itemLabel(fn (array $state): ?string => $state['nama_syarat'] ?? 'Jenis Permohonan Baru')
                    ->collapsible()
                    ->columnSpanFull()
                // END: Form Builder Integration
            ]);
    }

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