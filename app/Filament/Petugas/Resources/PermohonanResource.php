<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PermohonanResource\Pages;
use App\Filament\Petugas\Resources\PermohonanResource\RelationManagers;
use App\Models\Permohonan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Markdown;
use Filament\Notifications\Notification;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Manajemen Pelayanan';

    protected static ?string $navigationLabel = 'Permohonan Warga';
    protected static ?string $modelLabel = 'Permohonan';
    protected static ?string $pluralModelLabel = 'Permohonan-Permohonan';

    public static function form(Form $form): Form
    { 
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Section::make('Informasi Permohonan')
                            ->schema([
                                Forms\Components\TextInput::make('kode_permohonan')
                                    ->label('Kode Permohonan')
                                    ->readOnly()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Diajukan Oleh (Warga)')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('layanan_id')
                                    ->relationship('layanan', 'name')
                                    ->label('Jenis Layanan')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->readOnly()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Section::make('Data Pemohon')
                            ->schema([
                                Forms\Components\KeyValue::make('data_pemohon')
                                    ->keyLabel('Field')
                                    ->valueLabel('Nilai')
                                    ->label('Detail Data Pemohon')
                                    ->disabled()
                                    ->keyPlaceholder('Nama Field')
                                    ->valuePlaceholder('Nilai Field')
                                    ->columnSpanFull()
                                    ->hiddenOn('create'),
                            ]),

                        Section::make('Berkas Pemohon')
                            ->schema([
                                Repeater::make('berkas_pemohon')
                                    ->label('Daftar Berkas')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Berkas')
                                            ->readOnly(),
                                        FileUpload::make('path_dokumen')
                                            ->label('File')
                                            ->disabled()
                                            ->disk('private')
                                            ->directory('berkas-permohonan')
                                            ->visibility('private')
                                            ->downloadable()
                                            ->openable()
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (string $file): string => $file,
                                            ),
                                    ])
                                    ->columnSpanFull()
                                    ->hiddenOn('create'),
                            ]),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Section::make('Status & Catatan Petugas')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Permohonan')
                                    ->options([
                                        'baru' => 'Baru Diajukan',
                                        'sedang_ditinjau' => 'Sedang Ditinjau',
                                        'verifikasi_berkas' => 'Verifikasi Berkas',
                                        'diproses' => 'Sedang Diproses',
                                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
                                        'butuh_perbaikan' => 'Butuh Perbaikan',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull()
                                    ->live() // Membuat field reactive
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Auto-fill catatan berdasarkan status
                                        match($state) {
                                            'sedang_ditinjau' => $set('catatan_petugas', 'Permohonan sedang dalam tahap peninjauan oleh petugas.'),
                                            'verifikasi_berkas' => $set('catatan_petugas', 'Sedang melakukan verifikasi kelengkapan berkas.'),
                                            'membutuhkan_revisi' => $set('catatan_petugas', 'Permohonan membutuhkan revisi. Silakan periksa keterangan lebih lanjut.'),
                                            'butuh_perbaikan' => $set('catatan_petugas', 'Dokumen atau data perlu diperbaiki sebelum dapat diproses lebih lanjut.'),
                                            'ditolak' => $set('catatan_petugas', 'Permohonan tidak dapat disetujui. Alasan: '),
                                            default => null
                                        };
                                    }),
                                
                                Textarea::make('catatan_petugas')
                                    ->label('Catatan Petugas')
                                    ->placeholder('Tambahkan catatan internal mengenai permohonan ini...')
                                    ->rows(5)
                                    ->columnSpanFull()
                                    ->required(fn (Forms\Get $get) => 
                                        in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak'])
                                    )
                                    ->helperText(fn (Forms\Get $get) => 
                                        in_array($get('status'), ['membutuhkan_revisi', 'butuh_perbaikan', 'ditolak']) 
                                            ? 'Catatan wajib diisi untuk status ini.' 
                                            : 'Catatan opsional untuk dokumentasi internal.'
                                    ),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('user.name')
                    ->label('Warga')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('layanan.name')
                    ->label('Jenis Layanan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'gray',
                        'sedang_ditinjau' => 'info',
                        'verifikasi_berkas' => 'warning',
                        'diproses' => 'info',
                        'membutuhkan_revisi' => 'danger',
                        'butuh_perbaikan' => 'danger',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'primary',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'baru' => 'Baru Diajukan',
                        'sedang_ditinjau' => 'Sedang Ditinjau',
                        'verifikasi_berkas' => 'Verifikasi Berkas',
                        'diproses' => 'Sedang Diproses',
                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
                        'butuh_perbaikan' => 'Butuh Perbaikan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'baru' => 'Baru Diajukan',
                        'sedang_ditinjau' => 'Sedang Ditinjau',
                        'verifikasi_berkas' => 'Verifikasi Berkas',
                        'diproses' => 'Sedang Diproses',
                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
                        'butuh_perbaikan' => 'Butuh Perbaikan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                    ])
                    ->native(false),
                SelectFilter::make('layanan_id')
                    ->relationship('layanan', 'name')
                    ->label('Jenis Layanan')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // Bulk action untuk update status multiple permohonan
                    Tables\Actions\BulkAction::make('bulkUpdateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-m-pencil-square')
                        ->color('warning')
                        ->form([
                            Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'sedang_ditinjau' => 'Sedang Ditinjau',
                                    'verifikasi_berkas' => 'Verifikasi Berkas',
                                    'diproses' => 'Sedang Diproses',
                                ])
                                ->required()
                                ->native(false),
                            Textarea::make('catatan_petugas')
                                ->label('Catatan Petugas')
                                ->rows(3),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each(function (Permohonan $record) use ($data) {
                                $record->update([
                                    'status' => $data['status'],
                                    'catatan_petugas' => $data['catatan_petugas'],
                                ]);
                            });

                            Notification::make()
                                ->title('Status berhasil diupdate untuk ' . $records->count() . ' permohonan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('user.name')->label('Nama Warga'),
                        TextEntry::make('layanan.name')->label('Jenis Layanan'),
                        TextEntry::make('data_pemohon.jenis_permohonan')->label('Jenis Permohonan'),
                        TextEntry::make('status')
                            ->label('Status Permohonan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru' => 'gray',
                                'sedang_ditinjau' => 'info',
                                'verifikasi_berkas' => 'warning',
                                'diproses' => 'info',
                                'membutuhkan_revisi' => 'danger',
                                'butuh_perbaikan' => 'danger',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'primary',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'baru' => 'Baru Diajukan',
                                'sedang_ditinjau' => 'Sedang Ditinjau',
                                'verifikasi_berkas' => 'Verifikasi Berkas',
                                'diproses' => 'Sedang Diproses',
                                'membutuhkan_revisi' => 'Membutuhkan Revisi',
                                'butuh_perbaikan' => 'Butuh Perbaikan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'selesai' => 'Selesai',
                                default => $state,
                            }),
                        TextEntry::make('catatan_petugas')->label('Catatan Petugas')->markdown()->columnSpanFull(),
                        TextEntry::make('created_at')->label('Tanggal Pengajuan')->dateTime(),
                        TextEntry::make('updated_at')->label('Terakhir Diperbarui')->dateTime(),
                    ])
                    ->collapsed(),

                // Detail Data Diri Pemohon
                InfolistSection::make('Detail Data Diri Pemohon')
                    ->columns(3)
                    ->description('Informasi Lengkap Profile Pemohon')
                    ->schema([
                        TextEntry::make('user.nik')->label('NIK'),
                        TextEntry::make('user.nomor_kk')->label('Nomor KK'),
                        TextEntry::make('user.nomor_whatsapp')->label('Nomor WhatsApp'),
                        TextEntry::make('user.jenis_kelamin')->label('Jenis Kelamin'),
                        TextEntry::make('user.agama')->label('Agama'),
                        TextEntry::make('user.tempat_lahir')->label('Tempat Lahir'),
                        TextEntry::make('user.tanggal_lahir')->label('Tanggal Lahir')->date(),
                        TextEntry::make('user.gol_darah')->label('Golongan Darah'),
                        TextEntry::make('user.alamat')->label('Alamat Lengkap')->columnSpanFull(),
                        TextEntry::make('user.rt_rw')->label('RT/RW'),
                        TextEntry::make('user.desa_kelurahan')->label('Desa/Kelurahan'),
                        TextEntry::make('user.kecamatan')->label('Kecamatan'),
                        TextEntry::make('user.kabupaten')->label('Kabupaten'),
                        TextEntry::make('user.status_keluarga')->label('Status dalam Keluarga'),
                        TextEntry::make('user.status_perkawinan')->label('Status Perkawinan'),
                        TextEntry::make('user.pekerjaan')->label('Pekerjaan'),
                        TextEntry::make('user.pendidikan')->label('Pendidikan Terakhir'),
                    ])
                    ->collapsed(),

                InfolistSection::make('Berkas Permohonan')
                    ->schema(function (Permohonan $record) {
                        $berkasFields = [];
                        if (is_array($record->berkas_pemohon)) {
                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                if (empty($berkas['path_dokumen'])) continue;

                                $berkasFields[] = TextEntry::make("berkas_pemohon.{$index}.nama_dokumen")
                                    ->label('Nama Dokumen')
                                    ->url(fn() => route('secure.download', [
                                        'permohonan_id' => $record->id,
                                        'path' => $berkas['path_dokumen']
                                    ]), true)
                                    ->formatStateUsing(fn() => $berkas['nama_dokumen'] . ' (Unduh)')
                                    ->icon('heroicon-m-arrow-down-tray');
                            }
                        }
                        return $berkasFields;
                    })->columns(2),
            ]);
    }
    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\RevisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'view' => Pages\ViewPermohonan::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['kode_permohonan', 'user.name', 'layanan.name'];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}