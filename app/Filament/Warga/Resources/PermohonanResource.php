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
            Hidden::make('layanan_data'),
            Hidden::make('layanan_info'),
            Hidden::make('layanan_id')->required(),

            Wizard::make([
                Wizard\Step::make('1. Pilih Jenis Permohonan')
                    ->schema([
                        Placeholder::make('info_layanan')
                            ->label('Anda sedang mengajukan permohonan untuk layanan:')
                            ->content(fn (Get $get): string => 
                                $get('layanan_info.nama_kategori') . ' / ' . $get('layanan_info.nama_layanan')
                            ),
                        Radio::make('data_pemohon.jenis_permohonan')
                            ->label('Pilih Jenis Permohonan yang Sesuai')
                            ->required()
                            ->live()
                            ->options(function (Get $get) {
                                $layananData = $get('layanan_data');
                                return $layananData ? collect($layananData)->pluck('nama_syarat', 'nama_syarat') : [];
                            })
                            ->descriptions(function (Get $get) {
                                $layananData = $get('layanan_data');
                                if (!$layananData) {
                                    return [];
                                }
                                return collect($layananData)->mapWithKeys(function ($item) {
                                    return [$item['nama_syarat'] => new HtmlString($item['deskripsi_syarat'])];
                                })->all();
                            }),
                        
                        Section::make('Formulir untuk Diunduh')
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

                                if ($formulirs->isEmpty()) {
                                    return [];
                                }
                                
                                $placeholders = [];
                                foreach ($formulirs as $formulir) {
                                    $downloadUrl = route('formulir-master.download', $formulir);
                                    
                                    $placeholders[] = Placeholder::make('formulir_master_' . $formulir->id)
                                        ->label($formulir->nama_formulir)
                                        ->content(new HtmlString(
                                            '<a href="' . $downloadUrl . '" target="_blank" class="flex items-center gap-1 text-sm font-medium text-primary-600 hover:underline">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a6 6 0 00-6 6v3.586l-1.293 1.293a1 1 0 001.414 1.414L6 12.414V8a4 4 0 118 0v4.414l-1.293-1.293a1 1 0 00-1.414 1.414L10 14.828l-2.293-2.293a1 1 0 00-1.414 1.414L10 17.657l3.707-3.707a1 1 0 00-1.414-1.414L11 13.586V8a6 6 0 00-6-6z"></path></svg>
                                                Unduh Formulir (PDF)
                                            </a>'
                                        ));
                                }
                                return $placeholders;
                            }),
                    ]),
                
                Wizard\Step::make('2. Isi Formulir')
                    ->schema(function (Get $get): array {
                        $selectedJenis = $get('data_pemohon.jenis_permohonan');
                        $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);

                        if (empty($jenisData['form_fields'])) {
                             return [
                                Placeholder::make('no_form_placeholder')
                                    ->label('')
                                    ->content('Tidak ada data tambahan yang perlu diisi. Silakan lanjut ke langkah berikutnya.')
                            ];
                        }
                        
                        $formFieldsSchema = [];
                        foreach($jenisData['form_fields'] as $field) {
                            $fieldName = 'data_pemohon.' . $field['field_name'];
                            $fieldComponent = match ($field['field_type']) {
                                'textarea' => Textarea::make($fieldName),
                                'select' => Select::make($fieldName)->options(collect($field['field_options'])->pluck('label', 'value')),
                                'radio' => Radio::make($fieldName)->options(collect($field['field_options'])->pluck('label', 'value')),
                                'checkbox' => Checkbox::make($fieldName),
                                'date' => DatePicker::make($fieldName),
                                default => TextInput::make($fieldName)->type($field['field_type']),
                            };
                            
                            $formFieldsSchema[] = $fieldComponent
                                ->label($field['field_label'])
                                ->required((bool) ($field['is_required'] ?? false));
                        }
                        return [Section::make('Data Isian')->schema($formFieldsSchema)];
                    }),
                
                Wizard\Step::make('3. Unggah Dokumen')
                    ->schema(function (Get $get): array {
                        $selectedJenis = $get('data_pemohon.jenis_permohonan');
                        $jenisData = collect($get('layanan_data'))->firstWhere('nama_syarat', $selectedJenis);

                        if (empty($jenisData['file_requirements'])) {
                            return [
                                Placeholder::make('no_files_placeholder')
                                    ->label('')
                                    ->content('Tidak ada dokumen yang perlu diunggah untuk jenis permohonan ini.')
                            ];
                        }

                        $fileFieldsSchema = [];
                        foreach($jenisData['file_requirements'] as $fileReq) {
                            $fileKey = 'berkas_pemohon.' . $fileReq['file_key'];
                            $fileUpload = FileUpload::make($fileKey)
                                ->label($fileReq['file_name'])
                                ->helperText($fileReq['file_description'] ?? '')
                                ->required((bool) ($fileReq['is_required'] ?? false))
                                ->maxSize($fileReq['max_size'] ? $fileReq['max_size'] * 1024 : 2048)
                                ->disk('private')
                                ->directory('berkas-permohonan');
                            
                            $acceptedTypes = match($fileReq['file_type'] ?? 'any') {
                                'image' => ['image/jpeg', 'image/png'],
                                'pdf' => ['application/pdf'],
                                'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                                default => [],
                            };
                            
                            if (!empty($acceptedTypes)) {
                                $fileUpload->acceptedFileTypes($acceptedTypes);
                            }

                            $fileFieldsSchema[] = $fileUpload;
                        }

                        return [Section::make('Unggah Dokumen Wajib')->schema($fileFieldsSchema)];
                    })
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
                        'baru' => 'gray',
                        'sedang_ditinjau' => 'primary',
                        'diproses' => 'primary',
                        'verifikasi_berkas' => 'warning',
                        'membutuhkan_revisi' => 'danger',
                        'butuh_perbaikan' => 'danger',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Diajukan')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Update Terakhir')->since()->sortable(),
            ])
            ->filters([
                //
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
                                'baru' => 'gray',
                                'sedang_ditinjau' => 'info',
                                'diproses' => 'primary',
                                'verifikasi_berkas' => 'warning',
                                'membutuhkan_revisi' => 'danger',
                                'butuh_perbaikan' => 'danger',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'success',
                                default => 'gray',
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