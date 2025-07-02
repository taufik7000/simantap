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
                                        FileUpload::make('path_dokumen') // Menggunakan path_dokumen
                                            ->label('File')
                                            ->disabled()
                                            ->disk('private')
                                            ->directory('berkas-permohonan') // PERBAIKAN: Tambahkan ini untuk konsistensi
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
                                        'verifikasi_berkas' => 'Verifikasi Berkas',
                                        'diproses' => 'Sedang Diproses',
                                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull(),
                                Textarea::make('catatan_petugas')
                                    ->label('Catatan Petugas')
                                    ->placeholder('Tambahkan catatan internal mengenai permohonan ini...')
                                    ->rows(5)
                                    ->columnSpanFull(),
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
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Warga')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('layanan.name')
                    ->label('Jenis Layanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'info',
                        'verifikasi_berkas' => 'warning',
                        'diproses' => 'info',
                        'membutuhkan_revisi' => 'danger',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'primary',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'baru' => 'Baru Diajukan',
                        'verifikasi_berkas' => 'Verifikasi Berkas',
                        'diproses' => 'Sedang Diproses',
                        'membutuhkan_revisi' => 'Membutuhkan Revisi',
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->columns(3) // Tambahkan columns(3) seperti di resource warga
                    ->schema([
                        TextEntry::make('kode_permohonan')->label('Kode Permohonan'),
                        // Menggunakan Layanan.name karena ada relasi ke Layanan
                        TextEntry::make('layanan.name')->label('Jenis Layanan'),
                        // Asumsi data_pemohon memiliki 'jenis_permohonan'
                        TextEntry::make('data_pemohon.jenis_permohonan')->label('Jenis Permohonan'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru' => 'gray', // Atau info jika ingin warna biru/cyan
                                'verifikasi_berkas' => 'warning',
                                'diproses' => 'warning',
                                'membutuhkan_revisi' => 'danger',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'primary',
                                default => 'gray',
                            }),
                        TextEntry::make('catatan_petugas')->label('Catatan Petugas')->markdown()->columnSpanFull(), // Pastikan ini tetap ada jika diperlukan
                        TextEntry::make('created_at')->label('Tanggal Pengajuan')->dateTime(),
                        TextEntry::make('updated_at')->label('Terakhir Diperbarui')->dateTime(),
                    ]),

                InfolistSection::make('Data Detail Pemohon')
                    ->schema([
                        KeyValueEntry::make('data_pemohon') // Kembali ke KeyValueEntry
                            ->keyLabel('Field Data')
                            ->valueLabel('Nilai Data')
                            ->columnSpanFull(),
                    ]),

                InfolistSection::make('Berkas Terlampir') // Sesuaikan label
                    ->schema(function (Permohonan $record) { // Gunakan skema dinamis dari resource warga
                        $berkasFields = [];
                        if (is_array($record->berkas_pemohon)) {
                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                // Pastikan 'path_dokumen' dan 'nama_dokumen' ada di array berkas
                                if (empty($berkas['path_dokumen'])) continue;

                                $berkasFields[] = TextEntry::make("berkas_pemohon.{$index}.nama_dokumen") // Menggunakan nama_dokumen
                                    ->label('Nama Dokumen')
                                    ->url(fn() => route('secure.download', [
                                        'permohonan_id' => $record->id,
                                        'path' => $berkas['path_dokumen']
                                    ]), true) // true untuk membuka di tab baru
                                    ->formatStateUsing(fn() => $berkas['nama_dokumen'] . ' (Unduh)') // Tampilkan nama dan teks unduh
                                    ->icon('heroicon-m-arrow-down-tray'); // Ikon unduh
                            }
                        }
                        return $berkasFields;
                    })->columns(2), // Sesuaikan columns seperti di resource warga
            ]);
    }


    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'view' => Pages\ViewPermohonan::route('/{record}'),
            'edit' => Pages\EditPermohonan::route('/{record}/edit'),
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