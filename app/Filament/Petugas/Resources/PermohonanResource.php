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
use Filament\Forms\Components\KeyValue; // Untuk menampilkan data_pemohon
use Filament\Forms\Components\Repeater; // Untuk menampilkan berkas_pemohon
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload; // Untuk menampilkan berkas_pemohon
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

    // Label navigasi
    protected static ?string $navigationLabel = 'Permohonan Warga';

    // Label untuk model (singular/plural)
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
                                    ->readOnly()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('layanan_id')
                                    ->relationship('layanan', 'name')
                                    ->label('Jenis Layanan')
                                    ->readOnly()
                                    ->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->readOnly()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Section::make('Data Pemohon')
                            ->schema([
                                // Menggunakan KeyValue untuk menampilkan data_pemohon (JSON)
                                // Akan lebih baik jika Anda membuat Infolist di ViewPage daripada di sini
                                // Tapi untuk form Edit, kita hanya ingin data_pemohon ditampilkan readonly
                                Forms\Components\KeyValue::make('data_pemohon')
                                    ->keyLabel('Field')
                                    ->valueLabel('Nilai')
                                    ->label('Detail Data Pemohon')
                                    ->disabled() // Tidak bisa diedit di sini
                                    ->keyPlaceholder('Nama Field')
                                    ->valuePlaceholder('Nilai Field')
                                    ->columnSpanFull()
                                    ->hiddenOn('create'), // Hidden di halaman create karena petugas tidak membuat
                            ]),

                        Section::make('Berkas Pemohon')
                            ->schema([
                                // Menampilkan berkas_pemohon (JSON array of paths)
                                // Gunakan Repeater untuk menampilkan setiap berkas
                                Repeater::make('berkas_pemohon')
                                    ->label('Daftar Berkas')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null) // Menampilkan nama file jika ada
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Berkas')
                                            ->readOnly(),
                                        // Asumsi path tersimpan di key 'path'
                                        // Tampilkan sebagai link download atau preview
                                        FileUpload::make('path')
                                            ->label('File')
                                            ->readOnly()
                                            ->disk('public') // Sesuaikan dengan disk penyimpanan Anda
                                            ->visibility('private') // Atau 'public' jika berkas bisa diakses langsung
                                            ->downloadable()
                                            ->openable()
                                            ->getUploadedFileNameForStorageUsing(
                                                fn (string $file): string => $file, // Jangan ubah nama file asli saat menampilkan
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
                                    ->native(false) // Untuk tampilan lebih modern
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
                    ->badge() // Menampilkan status sebagai badge berwarna
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
                    ->toggleable(isToggledHiddenByDefault: false), // Tampilkan secara default
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
                // Tambahkan custom action untuk perubahan status cepat jika diinginkan
                // Tables\Actions\Action::make('approve')
                //     ->label('Setujui')
                //     ->color('success')
                //     ->requiresConfirmation()
                //     ->action(fn (Permohonan $record) => $record->update(['status' => 'disetujui']))
                //     ->visible(fn (Permohonan $record): bool => $record->status !== 'disetujui' && $record->status !== 'ditolak'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Infolist untuk halaman View
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->schema([
                        TextEntry::make('kode_permohonan')->label('Kode Permohonan'),
                        TextEntry::make('user.name')->label('Diajukan Oleh (Warga)'),
                        TextEntry::make('layanan.name')->label('Jenis Layanan'),
                        TextEntry::make('status')
                            ->label('Status Permohonan')
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
                            }),
                        TextEntry::make('catatan_petugas')->label('Catatan Petugas')->markdown()->columnSpanFull(),
                        TextEntry::make('created_at')->label('Tanggal Pengajuan')->dateTime(),
                        TextEntry::make('updated_at')->label('Terakhir Diperbarui')->dateTime(),
                    ])->columns(2),

                InfolistSection::make('Data Detail Pemohon')
                    ->schema([
                        // Ini akan menampilkan data_pemohon (JSON) sebagai Key-Value pair
                        KeyValueEntry::make('data_pemohon')
                            ->keyLabel('Field Data')
                            ->valueLabel('Nilai Data')
                            ->columnSpanFull(),
                    ]),

                InfolistSection::make('Berkas Permohonan')
                    ->schema([
                        RepeatableEntry::make('berkas_pemohon')
                            ->label('Daftar Berkas Terlampir')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Berkas'),
                                // Menampilkan link download atau preview gambar jika path mengarah ke file
                                TextEntry::make('path')
                                    ->label('File')
                                    ->formatStateUsing(function (string $state): HtmlString {
                                        $url = Storage::url($state); // Sesuaikan disk storage Anda
                                        $filename = basename($state);
                                        $extension = pathinfo($filename, PATHINFO_EXTENSION);

                                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            return new HtmlString("<a href='{$url}' target='_blank'><img src='{$url}' class='w-20 h-auto rounded' alt='{$filename}'></a>");
                                        }
                                        return new HtmlString("<a href='{$url}' target='_blank' class='text-primary-600 hover:underline'>{$filename}</a>");
                                    }),
                            ])->columnSpanFull()
                    ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            // Anda bisa menambahkan relation manager di sini di masa depan,
            // seperti history log permohonan.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            // Petugas tidak perlu membuat permohonan baru
            // 'create' => Pages\CreatePermohonan::route('/create'),
            'view' => Pages\ViewPermohonan::route('/{record}'),
            'edit' => Pages\EditPermohonan::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['kode_permohonan', 'user.name', 'layanan.name'];
    }

    // Nonaktifkan tombol 'Create' di resource listing page
    public static function canCreate(): bool
    {
        return false;
    }
}