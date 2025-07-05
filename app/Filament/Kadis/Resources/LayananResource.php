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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
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
                Section::make('Informasi Dasar Layanan')
                    ->description('Masukkan detail dasar untuk layanan ini secara umum.')
                    // [PERUBAHAN] Menambahkan latar belakang abu-abu muda untuk membedakannya dari halaman.
                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800/50'])
                    ->schema([
                        Select::make('kategori_layanan_id')
                            ->relationship('KategoriLayanan', 'name')
                            ->label('Kategori Layanan Induk')
                            ->searchable()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Layanan Utama')
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

                Section::make('Jenis-Jenis Permohonan Dalam Layanan Ini')
                    ->description('Definisikan satu atau lebih jenis permohonan yang tersedia dalam layanan ini.')
                    // [PERUBAHAN] Menambahkan latar belakang abu-abu muda untuk membedakannya dari halaman.
                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800/50'])
                    ->schema([
                        Repeater::make('description')
                            ->label('Jenis-Jenis Permohonan')
                            ->addActionLabel('Tambah Jenis Permohonan')
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['nama_syarat'] ?? 'Jenis Permohonan Baru')
                            ->schema([
                                Tabs::make('Tabs')->tabs([
                                    Tabs\Tab::make('Detail Permohonan')
                                        ->icon('heroicon-o-identification')
                                        ->schema([
                                            TextInput::make('nama_syarat')
                                                ->label('Nama Jenis Permohonan')
                                                ->helperText('Contoh: Pembuatan KTP Baru, Perpanjangan SIM A')
                                                ->required()
                                                ->columnSpanFull(),
                                            RichEditor::make('deskripsi_syarat')
                                                ->label('Deskripsi & Keterangan untuk Warga')
                                                ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                                                ->required(),
                                            Select::make('formulir_master_id')
                                                ->label('Lampirkan Formulir Master (PDF)')
                                                ->options(FormulirMaster::all()->pluck('nama_formulir', 'id'))
                                                ->multiple()
                                                ->searchable()
                                                ->placeholder('Kosongkan jika tidak ada formulir PDF'),
                                        ]),

                                    Tabs\Tab::make('Form Builder Dinamis')
                                        ->icon('heroicon-o-pencil-square')
                                        ->badge(fn ($get) => count($get('form_fields') ?? []))
                                        ->schema([
                                            Repeater::make('form_fields')
                                                ->label('Field Formulir untuk Diisi Warga')
                                                ->schema(self::getFormBuilderFields())
                                                ->addActionLabel('Tambah Field')
                                                ->reorderableWithButtons()
                                                ->collapsible()
                                                ->itemLabel(function (array $state): ?Htmlable {
                                                    if (empty($state['field_label'])) {
                                                        return new HtmlString('Field Baru');
                                                    }
                                                    $type = $state['field_type'] ? "<span style='background-color: #e5e7eb; color: #4b5563; font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; margin-left: 8px;'>" . ucfirst($state['field_type']) . "</span>" : '';
                                                    $required = ($state['is_required'] ?? false) ? "<span style='background-color: #fee2e2; color: #b91c1c; font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; margin-left: 8px;'>Wajib</span>" : '';
                                                    return new HtmlString("<strong>" . e($state['field_label']) . "</strong>" . $type . $required);
                                                }),
                                        ]),

                                    Tabs\Tab::make('Syarat Berkas Upload')
                                        ->icon('heroicon-o-document-arrow-up')
                                        ->badge(fn ($get) => count($get('file_requirements') ?? []))
                                        ->schema([
                                            Repeater::make('file_requirements')
                                                ->label('Berkas yang Wajib Di-upload Warga')
                                                ->schema(self::getFileRequirementFields())
                                                ->addActionLabel('Tambah Syarat Berkas')
                                                ->reorderableWithButtons()
                                                ->collapsible()
                                                ->itemLabel(fn (array $state): ?string => $state['file_name'] ?? 'Berkas Baru'),
                                        ]),
                                ])->columnSpanFull(),
                            ])->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function getFormBuilderFields(): array
    {
        return [
            Grid::make(6)->schema([
                TextInput::make('field_label')
                    ->label('Label Field (Untuk Warga)')
                    ->required()
                    ->placeholder('Contoh: Alamat Lengkap')
                    ->columnSpan(3),
                TextInput::make('field_name')
                    ->label('Nama Field (Untuk Sistem)')
                    ->required()
                    ->placeholder('Contoh: alamat_lengkap')
                    ->helperText('Gunakan huruf kecil & underscore.')
                    ->columnSpan(3),
                Select::make('field_type')
                    ->label('Tipe Field')
                    ->options([
                        'text' => 'Teks Pendek', 'textarea' => 'Teks Panjang',
                        'select' => 'Pilihan Dropdown', 'checkbox' => 'Checkbox',
                        'radio' => 'Radio Button', 'date' => 'Tanggal',
                        'number' => 'Angka', 'email' => 'Email',
                    ])
                    ->reactive()
                    ->required()
                    ->columnSpan(4),
                Toggle::make('is_required')
                    ->label('Wajib Diisi')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(1),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->columnSpan(1),
            ]),
            Section::make('Opsi Pilihan')
                ->description('Tambahkan opsi untuk tipe field Pilihan Dropdown, Radio, atau Checkbox.')
                ->collapsible()
                ->collapsed()
                ->visible(fn ($get) => in_array($get('field_type'), ['select', 'radio', 'checkbox']))
                ->schema([
                    Repeater::make('field_options')
                        ->label(false)
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('value')->label('Nilai (Sistem)')->required(),
                                TextInput::make('label')->label('Label (Warga)')->required(),
                            ])
                        ])
                        ->addActionLabel('Tambah Opsi'),
                ]),
        ];
    }

    protected static function getFileRequirementFields(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('file_name')->label('Nama Berkas')->required()->placeholder('Contoh: Scan KTP'),
                TextInput::make('file_key')->label('Key (Sistem)')->required()->placeholder('Contoh: file_ktp'),
            ]),
            Grid::make(3)->schema([
                Select::make('file_type')->label('Tipe File')->options(['image' => 'Gambar (JPG, PNG)', 'pdf' => 'PDF', 'document' => 'Dokumen (PDF, DOC, DOCX)', 'any' => 'Semua Tipe'])->default('pdf'),
                Toggle::make('is_required')->label('Wajib')->default(true),
                TextInput::make('max_size')->label('Ukuran Max (MB)')->numeric()->default(2),
            ]),
            Textarea::make('file_description')->label('Keterangan Tambahan')->placeholder('Petunjuk untuk warga...'),
        ];
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