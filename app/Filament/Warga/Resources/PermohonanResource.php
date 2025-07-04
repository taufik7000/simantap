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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PermohonanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Permohonan Saya';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    /**
     * PENTING: Method ini memfilter data agar warga hanya melihat permohonan miliknya.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    /**
     * PENTING: Set route key untuk view berdasarkan kode_permohonan
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return 'kode_permohonan';
    }

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
                Tables\Columns\TextColumn::make('kode_permohonan')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode permohonan disalin!')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('data_pemohon.jenis_permohonan')
                    ->label('Jenis Permohonan')
                    ->wrap()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('layanan.name')
                    ->label('Kategori Layanan')
                    ->wrap()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baru' => 'gray',
                        'sedang_ditinjau' => 'info',
                        'verifikasi_berkas' => 'warning',
                        'diproses' => 'primary',
                        'membutuhkan_revisi' => 'danger',
                        'butuh_perbaikan' => 'danger',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Permohonan::STATUS_OPTIONS)
                    ->native(false),
                    
                Tables\Filters\SelectFilter::make('layanan_id')
                    ->relationship('layanan', 'name')
                    ->label('Kategori Layanan')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('perbaiki_permohonan')
                    ->label('Perbaiki Permohonan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Permohonan $record): bool => $record->canBeRevised())
                    ->form([
                        Forms\Components\Textarea::make('catatan_revisi')
                            ->label('Catatan Perbaikan')
                            ->placeholder('Jelaskan perbaikan yang Anda lakukan...')
                            ->rows(3)
                            ->required()
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
                                    ->acceptedFileTypes(['image/*', 'application/pdf', '.doc', '.docx'])
                                    ->maxSize(5120) // 5MB
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
                            ->body('Revisi Anda akan segera ditinjau oleh petugas kami.')
                            ->success()
                            ->sendToDatabase(Auth::user());
                    })
                    ->modalHeading('Kirim Perbaikan Permohonan')
                    ->modalDescription('Unggah dokumen yang sudah diperbaiki sesuai catatan dari petugas.')
                    ->modalSubmitActionLabel('Kirim Revisi'),
                    
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Permohonan')
            ->emptyStateDescription('Anda belum pernah mengajukan permohonan.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Permohonan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('kode_permohonan')
                            ->label('Kode Permohonan')
                            ->copyable()
                            ->copyMessage('Kode permohonan disalin!')
                            ->icon('heroicon-s-document-text'),
                            
                        TextEntry::make('layanan.name')
                            ->label('Kategori Layanan')
                            ->icon('heroicon-s-tag'),
                            
                        TextEntry::make('data_pemohon.jenis_permohonan')
                            ->label('Jenis Permohonan')
                            ->icon('heroicon-s-clipboard-document-list'),
                            
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baru' => 'gray',
                                'sedang_ditinjau' => 'info',
                                'verifikasi_berkas' => 'warning',
                                'diproses' => 'primary',
                                'membutuhkan_revisi' => 'danger',
                                'butuh_perbaikan' => 'danger',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state),
                            
                        TextEntry::make('created_at')
                            ->label('Tanggal Diajukan')
                            ->dateTime('d M Y H:i')
                            ->icon('heroicon-s-calendar'),
                            
                        TextEntry::make('updated_at')
                            ->label('Terakhir Update')
                            ->since()
                            ->icon('heroicon-s-clock'),
                    ]),

                InfolistSection::make('Catatan dari Petugas')
                    ->schema([
                        TextEntry::make('catatan_petugas')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Belum ada catatan dari petugas.')
                            ->color(fn ($state, Permohonan $record) => match($record->status) {
                                'membutuhkan_revisi', 'butuh_perbaikan', 'ditolak' => 'danger',
                                'disetujui', 'selesai' => 'success',
                                default => 'primary',
                            }),
                    ])
                    ->visible(fn (Permohonan $record) => !empty($record->catatan_petugas)),
                
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
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->collapsible()
                                    ->schema(function (Permohonan $record) {
                                        $berkasFields = [];
                                        if (is_array($record->berkas_pemohon)) {
                                            foreach ($record->berkas_pemohon as $index => $berkas) {
                                                if (empty($berkas['path_dokumen'])) continue;
                                                $berkasFields[] = TextEntry::make("berkas_pemohon.{$index}.nama_dokumen")
                                                    ->label(false)
                                                    ->url(fn() => route('secure.download', [
                                                        'permohonan_id' => $record->id, 
                                                        'path' => $berkas['path_dokumen']
                                                    ]), true)
                                                    ->formatStateUsing(fn() => $berkas['nama_dokumen'] . ' (Unduh)')
                                                    ->icon('heroicon-m-arrow-down-tray')
                                                    ->color('primary');
                                            }
                                        }
                                        return $berkasFields ?: [
                                            TextEntry::make('no_files')
                                                ->label('')
                                                ->default('Tidak ada berkas')
                                                ->color('gray')
                                        ];
                                    }),
                                    
                                InfolistSection::make('Riwayat Revisi yang Dikirim')
                                    ->icon('heroicon-o-arrow-path')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema(
                                        fn (Permohonan $record) => $record->revisions->map(function (PermohonanRevision $revision) {
                                            return InfolistSection::make("Revisi Ke-{$revision->revision_number}")
                                                ->schema([
                                                    TextEntry::make('status_revisi')
                                                        ->label('Status Revisi')
                                                        ->badge()
                                                        ->color(fn (): string => $revision->getStatusColorAttribute())
                                                        ->default($revision->getStatusLabelAttribute()),
                                                        
                                                    TextEntry::make('tanggal_kirim')
                                                        ->label('Tanggal Kirim')
                                                        ->default($revision->created_at->format('d M Y H:i')),
                                                        
                                                    TextEntry::make('catatan_anda')
                                                        ->label('Catatan yang Anda Kirim')
                                                        ->default($revision->catatan_revisi)
                                                        ->columnSpanFull()
                                                        ->visible(fn() => !empty($revision->catatan_revisi)),
                                                        
                                                    TextEntry::make('feedback_petugas')
                                                        ->label('Feedback dari Petugas')
                                                        ->default($revision->catatan_petugas)
                                                        ->columnSpanFull()
                                                        ->color(match($revision->status) {
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger',
                                                            default => 'primary',
                                                        })
                                                        ->visible(fn() => !empty($revision->catatan_petugas)),
                                                        
                                                    // Dokumen revisi
                                                    ...array_filter(array_map(function ($berkas, $index) use ($revision) {
                                                        if (empty($berkas['path_dokumen'])) return null;
                                                        return TextEntry::make("revisi_{$revision->id}_berkas_{$index}")
                                                            ->label('Dokumen: ' . $berkas['nama_dokumen'])
                                                            ->url(fn() => route('secure.download.revision', [
                                                                'revision_id' => $revision->id,
                                                                'path' => $berkas['path_dokumen']
                                                            ]), true)
                                                            ->default('Unduh')
                                                            ->icon('heroicon-m-arrow-down-tray')
                                                            ->color('primary');
                                                    }, $revision->berkas_revisi ?? [], array_keys($revision->berkas_revisi ?? [])))
                                                ])
                                                ->columns(2)
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

    /**
     * PERBAIKAN UTAMA: Urutan route yang benar untuk menghindari konflik 404
     * create harus di atas view agar tidak tertangkap oleh parameter {record}
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonans::route('/'),
            'view' => Pages\ViewPermohonan::route('/{record:kode_permohonan}'), // Gunakan kode_permohonan sebagai key
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        $activeCount = static::getEloquentQuery()
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->count();
            
        return $activeCount > 0 ? (string) $activeCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $needsAttentionCount = static::getEloquentQuery()
            ->whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan'])
            ->count();
            
        return $needsAttentionCount > 0 ? 'danger' : 'primary';
    }
}