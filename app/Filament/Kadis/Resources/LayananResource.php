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
use Filament\Tables\Columns\IconColumn;

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
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        Select::make('icon')
                            ->label('Pilih Ikon Layanan')
                            ->options(self::getIconOptions()) // Mengambil daftar ikon dari fungsi helper
                            ->searchable()
                            ->allowHtml()
                            ->helperText('Pilih ikon yang paling sesuai untuk mewakili layanan ini.'),
   
                        RichEditor::make('deskripsi_layanan')
                            ->label('Deskripsi Singkat Layanan (untuk Tampilan Publik)')
                            ->helperText('Deskripsi ini akan muncul di halaman "Semua Layanan".')
                            ->toolbarButtons(),
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
                            ->itemLabel(fn(array $state): ?string => $state['nama_syarat'] ?? 'Jenis Permohonan Baru')
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
                                                ->toolbarButtons([
                                                    'h1', 
                                                    'h2',
                                                    'h3',
                                                    'bold',
                                                    'italic',
                                                    'bulletList',
                                                    'orderedList',
                                                    'link'
                                                ])
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
                                        ->badge(fn($get) => count($get('form_fields') ?? []))
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
                                        ->badge(fn($get) => count($get('file_requirements') ?? []))
                                        ->schema([
                                            Repeater::make('file_requirements')
                                                ->label('Berkas yang Wajib Di-upload Warga')
                                                ->schema(self::getFileRequirementFields())
                                                ->addActionLabel('Tambah Syarat Berkas')
                                                ->reorderableWithButtons()
                                                ->collapsible()
                                                ->itemLabel(fn(array $state): ?string => $state['file_name'] ?? 'Berkas Baru'),
                                        ]),
                                ])->columnSpanFull(),
                            ])->columnSpanFull(),
                    ]),
            ]);
            
    }

    public static function getIconOptions(): array
    {
        // Daftar ikon ini sama persis dengan yang ada di KategoriLayananResource
        return [
            'heroicon-o-briefcase' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"></path></svg><span>Briefcase</span></div>',
            'heroicon-o-building-office' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg><span>Building Office</span></div>',
            'heroicon-o-users' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-1a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span>Users</span></div>',
            'heroicon-o-document-text' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span>Document</span></div>',
            'heroicon-o-cog-6-tooth' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span>Settings</span></div>',
            'heroicon-o-home' => '<div class="flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg><span>Home</span></div>',
            
        ];
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
                ->visible(fn($get) => in_array($get('field_type'), ['select', 'radio', 'checkbox']))
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
                IconColumn::make('icon')
                    ->label('Ikon')
                    ->icon(fn ($record) => $record->icon),
                Tables\Columns\TextColumn::make('name')->label('Nama Layanan')->searchable(),
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