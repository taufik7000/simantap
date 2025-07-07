<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\PermohonanResource\Pages;
use App\Models\FormulirMaster;
use App\Models\Permohonan;
use App\Models\PermohonanRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Permohonan Saya';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Field tersembunyi untuk data sementara
            Forms\Components\Hidden::make('layanan_data'),
            Forms\Components\Hidden::make('layanan_info'),
            Forms\Components\Hidden::make('layanan_id')->required(),

            Forms\Components\Wizard::make([

                // WIZARD 1: PILIH JENIS PERMOHONAN
                Forms\Components\Wizard\Step::make('Pilih Jenis Permohonan')
                    ->schema([
                        Forms\Components\Placeholder::make('info_layanan')
                            ->label('Anda sedang mengajukan permohonan untuk layanan:')
                            ->content(fn (Get $get): string => 
                                $get('layanan_info.nama_kategori') . ' / ' . $get('layanan_info.nama_layanan')
                            ),
                        
                        ViewField::make('data_pemohon.jenis_permohonan')
                            ->label('Pilih jenis permohonan yang sesuai')
                            ->view('filament.forms.components.card-radio')
                            ->viewData(function (Get $get) {
                                $options = [];
                                if ($layananData = $get('layanan_data')) {
                                    $options = collect($layananData)->pluck('nama_syarat', 'nama_syarat');
                                }
                                return ['options' => $options];
                            })
                            ->required()
                            ->live(),
                    ]),

                // WIZARD 2: KETERANGAN & FORMULIR UNDUH
                Forms\Components\Wizard\Step::make('Keterangan & Formulir')
                    ->schema([
                        Forms\Components\Placeholder::make('deskripsi_jenis_permohonan')
                            ->label('Keterangan')
                            ->content(function (Get $get): HtmlString {
                                $selectedJenis = $get('data_pemohon.jenis_permohonan');
                                $layananData = $get('layanan_data');
                                $description = 'Pilih jenis permohonan terlebih dahulu untuk melihat keterangan.';

                                if ($selectedJenis && $layananData) {
                                    $jenisData = collect($layananData)->firstWhere('nama_syarat', $selectedJenis);
                                    if ($jenisData && !empty($jenisData['deskripsi_syarat'])) {
                                        $description = $jenisData['deskripsi_syarat'];
                                    }
                                }
                                return new HtmlString($description);
                            }),

                        Forms\Components\Section::make('Formulir untuk Diunduh')
                            ->description('Jika ada, silakan unduh, isi, dan unggah kembali formulir berikut di langkah selanjutnya.')
                            ->collapsible()
                            ->collapsed(false)
                            ->visible(function (Get $get): bool {
                                $selectedJenis = $get('data_pemohon.jenis_permohonan');
                                if (!$selectedJenis) return false;
                                
                                $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);
                                return !empty($jenisData['formulir_master_id']);
                            })
                            ->schema(function (Get $get): array {
                                $selectedJenis = $get('data_pemohon.jenis_permohonan');
                                if (!$selectedJenis) return [];

                                $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);
                                $formulirMasterIds = (array) ($jenisData['formulir_master_id'] ?? []);
                                $formulirs = FormulirMaster::whereIn('id', $formulirMasterIds)->get();

                                if ($formulirs->isEmpty()) return [];
                                
                                $placeholders = [];
                                foreach ($formulirs as $formulir) {
                                    $downloadUrl = route('formulir-master.download', $formulir);
                                    $placeholders[] = Forms\Components\Placeholder::make('formulir_master_' . $formulir->id)
                                        ->label($formulir->nama_formulir)
                                        ->content(new HtmlString(
                                            '<a href="' . $downloadUrl . '" target="_blank" class="flex items-center gap-1 text-sm font-medium text-primary-600 hover:underline">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                Unduh Formulir (PDF)
                                            </a>'
                                        ));
                                }
                                return $placeholders;
                            }),
                    ]),

                // WIZARD 3: ISI FORMULIR
                Forms\Components\Wizard\Step::make('Isi Formulir')
                    ->schema(function (Get $get): array {
                        $selectedJenis = $get('data_pemohon.jenis_permohonan');
                        $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);

                        if (empty($jenisData['form_fields'])) {
                             return [Forms\Components\Placeholder::make('no_form_placeholder')->content('Tidak ada data tambahan yang perlu diisi. Silakan lanjut ke langkah berikutnya.')];
                        }
                        
                        $formFieldsSchema = [];
                        foreach($jenisData['form_fields'] as $field) {
                            $fieldName = 'data_pemohon.' . $field['field_name'];
                            $fieldComponent = match ($field['field_type']) {
                                'textarea' => Forms\Components\Textarea::make($fieldName),
                                'select' => Forms\Components\Select::make($fieldName)->options(collect($field['field_options'])->pluck('label', 'value')),
                                'radio' => Forms\Components\Radio::make($fieldName)->options(collect($field['field_options'])->pluck('label', 'value')),
                                'checkbox' => Forms\Components\Checkbox::make($fieldName),
                                'date' => Forms\Components\DatePicker::make($fieldName),
                                default => Forms\Components\TextInput::make($fieldName)->type($field['field_type']),
                            };
                            
                            $formFieldsSchema[] = $fieldComponent->label($field['field_label'])->required((bool) ($field['is_required'] ?? false));
                        }
                        return [Forms\Components\Section::make('Data Isian')->schema($formFieldsSchema)];
                    }),

                // WIZARD 4: UNGGAH DOKUMEN
                Forms\Components\Wizard\Step::make('Unggah Dokumen')
                ->schema(function (Get $get): array {
                    $selectedJenis = $get('data_pemohon.jenis_permohonan');
                    $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);

                    if (empty($jenisData['file_requirements'])) {
                        $schema = [Forms\Components\Placeholder::make('no_files_placeholder')->content('Tidak ada dokumen yang perlu diunggah untuk jenis permohonan ini.')];
                    } else {
                        $fileFieldsSchema = [];
                        foreach ($jenisData['file_requirements'] as $fileReq) {
                            $fileKey = 'berkas_pemohon.' . $fileReq['file_key'];
                            $fileFieldsSchema[] = Forms\Components\FileUpload::make($fileKey)
                                ->label($fileReq['file_name'])
                                ->helperText($fileReq['file_description'] ?? '')
                                ->required((bool) ($fileReq['is_required'] ?? false))
                                ->maxSize($fileReq['max_size'] ? $fileReq['max_size'] * 1024 : 2048)
                                ->disk('private')
                                ->directory('berkas-permohonan');
                        }
                        $schema = [Forms\Components\Section::make('Unggah Dokumen Wajib')->schema($fileFieldsSchema)];
                    }

                    // --- TAMBAHKAN KODE INI UNTUK TOMBOL kirim ---
                    $schema[] = Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('submit')
                            ->label('Ajukan Permohonan')
                            ->icon('heroicon-o-paper-airplane')
                            ->color('primary')
                            ->size('lg')
                            ->action('create')
                            ->keyBindings(['mod+s']),
                    ])->alignRight();
                    
                    return $schema;
                }),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('data_pemohon.jenis_permohonan')
                    ->label('Jenis Permohonan')
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baru', 'dibatalkan' => 'gray',
                        'menunggu_verifikasi', 'proses_verifikasi' => 'info',
                        'proses_entri', 'entri_data_selesai' => 'warning',
                        'menunggu_persetujuan', 'proses_pengiriman' => 'primary',
                        'disetujui', 'dokumen_diterbitkan', 'selesai' => 'success',
                        'butuh_revisi', 'ditolak' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Diajukan')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Update Terakhir')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Permohonan::STATUS_OPTIONS)
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
         return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('Layanan.name')->label('Kategori Layanan'),
                        TextEntry::make('data_pemohon.jenis_permohonan')->label('Jenis Permohonan'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru', 'dibatalkan' => 'gray',
                                'menunggu_verifikasi', 'proses_verifikasi' => 'info',
                                'proses_entri', 'entri_data_selesai' => 'warning',
                                'menunggu_persetujuan', 'proses_pengiriman' => 'primary',
                                'disetujui', 'dokumen_diterbitkan', 'selesai' => 'success',
                                'butuh_revisi', 'ditolak' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state),
                        TextEntry::make('catatan_petugas')->label('Catatan Petugas')->markdown()->columnSpanFull()->visible(fn ($state) => !empty($state)),
                        TextEntry::make('created_at')->label('Tanggal Diajukan')->dateTime(),
                    ]),
                
                InfolistGrid::make(3)
                    ->schema([
                        InfolistGroup::make()
                            ->schema([
                                InfolistSection::make('Data Isian Formulir')
                                    ->collapsible()
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema(function (Permohonan $record) {
                                        $fields = [];
                                        $jenisPermohonan = $record->data_pemohon['jenis_permohonan'] ?? null;
                                        if ($jenisPermohonan && $record->layanan?->description) {
                                            $formDefinition = collect($record->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);
                                            if (!empty($formDefinition['form_fields'])) {
                                                foreach ($formDefinition['form_fields'] as $fieldDef) {
                                                    $fieldName = $fieldDef['field_name'];
                                                    if (isset($record->data_pemohon[$fieldName])) {
                                                        $fields[] = TextEntry::make("data_pemohon.{$fieldName}")->label($fieldDef['field_label']);
                                                    }
                                                }
                                            }
                                        }
                                        return empty($fields) ? [TextEntry::make('no_data_isian')->state('Tidak ada data isian tambahan untuk permohonan ini.')] : $fields;
                                    })->columns(2),
                                InfolistSection::make('Riwayat Permohonan')
                                    ->icon('heroicon-o-clock')
                                    ->schema([ViewEntry::make('logs')->label('')->view('filament.infolists.components.timeline-log')]),
                            ])->columnSpan(2),

                        InfolistGroup::make()
                            ->schema([
                                InfolistSection::make('Berkas Permohonan Awal')
                                    ->schema(function (Permohonan $record) {
                                        $berkasFields = [];
                                        $jenisPermohonan = $record->data_pemohon['jenis_permohonan'] ?? null;
                                        
                                        if (!$jenisPermohonan || !$record->layanan?->description) {
                                            return [TextEntry::make('no_berkas')->state('Definisi layanan tidak ditemukan.')];
                                        }
                                        
                                        $jenisData = collect($record->layanan->description)->firstWhere('nama_syarat', $jenisPermohonan);

                                        if (empty($jenisData['file_requirements'])) {
                                            return [TextEntry::make('no_berkas')->state('Tidak ada syarat berkas untuk permohonan ini.')];
                                        }

                                        foreach ($jenisData['file_requirements'] as $fileReq) {
                                            $fileKey = $fileReq['file_key'];
                                            $filePath = $record->berkas_pemohon[$fileKey] ?? null;
                                            
                                            $entry = TextEntry::make($fileKey)->label($fileReq['file_name']);

                                            if ($filePath) {
                                                $entry->state('Unduh Berkas')
                                                      ->color('primary')
                                                      ->url(route('secure.download', ['permohonan_id' => $record->id, 'path' => $filePath]), true)
                                                      ->icon('heroicon-m-arrow-down-tray');
                                            } else {
                                                $entry->state('Tidak diunggah')
                                                      ->color('danger')
                                                      ->icon('heroicon-o-x-circle');
                                            }
                                            $berkasFields[] = $entry;
                                        }
                                        return $berkasFields;
                                    }),
                                InfolistSection::make('Riwayat Revisi yang Dikirim')
                                    ->schema(
                                        fn (Permohonan $record) => $record->revisions->map(function (PermohonanRevision $revision) {
                                            return InfolistSection::make("Revisi Ke-{$revision->revision_number}")
                                                ->schema([
                                                    TextEntry::make('status_revisi')
                                                        ->label('Status Revisi')
                                                        ->badge()
                                                        ->color(fn (): string => $revision->getStatusColorAttribute())
                                                        ->default($revision->getStatusLabelAttribute()),
                                                    TextEntry::make('catatan_anda')
                                                        ->label('Catatan yang Anda Kirim')
                                                        ->default($revision->catatan_revisi)
                                                        ->columnSpanFull()
                                                        ->visible(fn() => !empty($revision->catatan_revisi)),
                                                    TextEntry::make('feedback_petugas')
                                                        ->label('Feedback dari Petugas')
                                                        ->default($revision->catatan_petugas)
                                                        ->columnSpanFull()
                                                        ->visible(fn() => !empty($revision->catatan_petugas)),
                                                    ...array_map(function ($berkas, $index) use ($revision) {
                                                        if (empty($berkas['path_dokumen'])) return null;
                                                        return TextEntry::make("revisi_{$revision->id}_berkas_{$index}")
                                                            ->label(false)
                                                            ->url(fn() => route('secure.download.revision', ['revision_id' => $revision->id, 'path' => $berkas['path_dokumen']]), true)
                                                            ->default($berkas['nama_dokumen'] . ' (Unduh)')
                                                            ->icon('heroicon-m-arrow-down-tray');
                                                    }, $revision->berkas_revisi ?? [], array_keys($revision->berkas_revisi ?? []))
                                                ])
                                                ->columns(1)
                                                ->collapsible()
                                                ->collapsed(fn() => $revision->status !== 'pending');
                                        })->all()
                                    )
                                    ->visible(fn (Permohonan $record) => $record->revisions->isNotEmpty()),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'create' => Pages\CreatePermohonan::route('/create'),
            'view' => Pages\ViewPermohonan::route('/{record:kode_permohonan}'),
        ];
    }
}