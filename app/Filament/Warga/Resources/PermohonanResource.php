<?php

namespace App\Filament\Warga\Resources;

use App\Filament\Warga\Resources\PermohonanResource\Pages;
use App\Models\Permohonan;
use App\Models\PermohonanRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Permohonan Saya';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('data_pemohon.jenis_permohonan')->required(),
                Forms\Components\Repeater::make('berkas_pemohon')
                    ->label(false)
                    ->addActionLabel('Tambah Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('nama_dokumen')
                            ->label('Nama Dokumen')
                            ->required(),
                        Forms\Components\FileUpload::make('path_dokumen')
                            ->label('Pilih File')
                            ->disk('private')
                            ->directory('berkas-permohonan')
                            ->required(),
                    ])
                    ->columns(1)
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->required()
                    ->hidden(fn ($get) => !$get('data_pemohon.jenis_permohonan')),
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
                Tables\Actions\Action::make('perbaiki_permohonan')
                    ->label('Perbaiki Permohonan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    // --- PERUBAHAN DI SINI: Menyederhanakan aturan visibilitas ---
                    ->visible(fn (Permohonan $record): bool => $record->canBeRevised())
                    ->form([
                        Forms\Components\Textarea::make('catatan_revisi')
                            ->label('Catatan Perbaikan')
                            ->placeholder('Jelaskan perbaikan yang Anda lakukan...')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('berkas_revisi')
                            ->label('Unggah Dokumen Perbaikan')
                            ->schema([
                                Forms\Components\TextInput::make('nama_dokumen')
                                    ->label('Nama Dokumen')
                                    ->required(),
                                Forms\Components\FileUpload::make('path_dokumen')
                                    ->label('Pilih File Revisi')
                                    ->disk('private')
                                    ->directory('berkas-revisi')
                                    ->required(),
                            ])
                            ->addActionLabel('Tambah Dokumen')
                            ->columnSpanFull()
                            ->minItems(1),
                    ])
                    ->action(function (Permohonan $record, array $data): void {
                        PermohonanRevision::create([
                            'permohonan_id' => $record->id,
                            'user_id' => Auth::id(),
                            'catatan_revisi' => $data['catatan_revisi'],
                            'berkas_revisi' => $data['berkas_revisi'],
                            'status' => 'pending',
                        ]);
                        $record->update([
                            'status' => 'sedang_ditinjau',
                            'catatan_petugas' => 'Warga telah mengirimkan revisi. Menunggu review petugas.',
                        ]);
                        Notification::make()
                            ->title('Revisi Berhasil Dikirim!')
                            ->success()
                            ->sendToDatabase(Auth::user());
                    })
                    ->modalHeading('Kirim Perbaikan Permohonan')
                    ->modalSubmitActionLabel('Kirim'),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    // (Method infolist, getRelations, dan getPages tidak ada perubahan)
    public static function infolist(Infolist $infolist): Infolist
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
                                InfolistSection::make('Riwayat Permohonan')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        ViewEntry::make('logs')
                                            ->label('')
                                            ->view('filament.infolists.components.timeline-log'),
                                    ]),
                            ])->columnSpan(2),
                        InfolistGroup::make()
                            ->schema([
                                InfolistSection::make('Berkas Permohonan Awal')
                                    ->schema(function (Permohonan $record) {
                                        $berkasFields = [];
                                        if (is_array($record->berkas_pemohon)) {
                                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                                if (empty($berkas['path_dokumen'])) continue;
                                                $berkasFields[] = TextEntry::make("berkas_pemohon.{$index}.nama_dokumen")
                                                    ->label(false)
                                                    ->url(fn() => route('secure.download', ['permohonan_id' => $record->id, 'path' => $berkas['path_dokumen']]), true)
                                                    ->formatStateUsing(fn() => $berkas['nama_dokumen'] . ' (Unduh)')
                                                    ->icon('heroicon-m-arrow-down-tray');
                                            }
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