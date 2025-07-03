<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PermohonanResource\Pages;
use App\Filament\Petugas\Resources\PermohonanResource\RelationManagers;
use App\Models\Permohonan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
                        Forms\Components\Section::make('Informasi Permohonan')
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

                        // SECTION BARU: Informasi Assignment
                        Forms\Components\Section::make('Informasi Penugasan')
                            ->schema([
                                Forms\Components\Select::make('assigned_to')
                                    ->label('Ditugaskan Kepada')
                                    ->options(function () {
                                        return User::role(['petugas', 'admin', 'kadis'])
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Belum ditugaskan')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $set('assigned_at', now());
                                            $set('assigned_by', Auth::id());
                                        } else {
                                            $set('assigned_at', null);
                                            $set('assigned_by', null);
                                        }
                                    }),
                                    
                                Forms\Components\DateTimePicker::make('assigned_at')
                                    ->label('Tanggal Ditugaskan')
                                    ->disabled(),
                                    
                                Forms\Components\Select::make('assigned_by')
                                    ->label('Ditugaskan Oleh')
                                    ->relationship('assignedBy', 'name')
                                    ->disabled(),
                            ])->columns(3)
                            ->visible(fn () => Auth::user()->hasRole(['admin', 'kadis'])),

                        Forms\Components\Section::make('Data Pemohon')
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

                        Forms\Components\Section::make('Berkas Pemohon')
                            ->schema([
                                Forms\Components\Repeater::make('berkas_pemohon')
                                    ->label('Daftar Berkas')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Berkas')
                                            ->readOnly(),
                                        Forms\Components\FileUpload::make('path_dokumen')
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
                        Forms\Components\Section::make('Status & Catatan Petugas')
                            ->schema([
                                Forms\Components\Select::make('status')
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
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        match($state) {
                                            'sedang_ditinjau' => $set('catatan_petugas', 'Permohonan sedang dalam tahap peninjauan oleh petugas.'),
                                            'verifikasi_berkas' => $set('catatan_petugas', 'Sedang melakukan verifikasi kelengkapan berkas.'),
                                            'membutuhkan_revisi' => $set('catatan_petugas', 'Permohonan membutuhkan revisi. Silakan periksa keterangan lebih lanjut.'),
                                            'butuh_perbaikan' => $set('catatan_petugas', 'Dokumen atau data perlu diperbaiki sebelum dapat diproses lebih lanjut.'),
                                            'ditolak' => $set('catatan_petugas', 'Permohonan tidak dapat disetujui. Alasan: '),
                                            default => null
                                        };
                                    }),
                                
                                Forms\Components\Textarea::make('catatan_petugas')
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

                        // SECTION BARU: Statistik Assignment (untuk Admin)
                        Forms\Components\Section::make('Statistik Workload')
                            ->schema([
                                Forms\Components\Placeholder::make('current_workload')
                                    ->label('Workload Anda Saat Ini')
                                    ->content(fn () => Permohonan::getPetugasWorkload(Auth::id()) . ' permohonan aktif'),
                                    
                                Forms\Components\Placeholder::make('total_unassigned')
                                    ->label('Permohonan Belum Ditugaskan')
                                    ->content(fn () => Permohonan::unassigned()->count() . ' permohonan'),
                                    
                                Forms\Components\Placeholder::make('overdue_assignments')
                                    ->label('Assignment Overdue')
                                    ->content(fn () => Permohonan::overdueAssignment(72)->count() . ' permohonan')
                                    ->extraAttributes(['class' => 'text-red-600']),
                            ])
                            ->visible(fn () => Auth::user()->hasRole(['admin', 'kadis']))
                            ->collapsible(),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')
                    ->label('Kode Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Warga')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('layanan.name')
                    ->label('Jenis Layanan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                // KOLOM BARU: Assignment Info
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Ditugaskan Ke')
                    ->default('Belum ditugaskan')
                    ->badge()
                    ->color(fn ($state) => $state === 'Belum ditugaskan' ? 'gray' : 'primary')
                    ->icon(fn ($state) => $state === 'Belum ditugaskan' ? 'heroicon-o-user-minus' : 'heroicon-o-user-check')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('assignment_duration')
                    ->label('Durasi Assignment')
                    ->getStateUsing(function (Permohonan $record): ?string {
                        if (!$record->assigned_at) {
                            return null;
                        }
                        $hours = $record->assignment_duration;
                        if ($hours < 24) {
                            return $hours . ' jam';
                        }
                        return round($hours / 24, 1) . ' hari';
                    })
                    ->badge()
                    ->color(function (Permohonan $record): string {
                        if (!$record->assigned_at) return 'gray';
                        $hours = $record->assignment_duration;
                        if ($hours > 72) return 'danger';
                        if ($hours > 48) return 'warning';
                        return 'success';
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
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
                    ->formatStateUsing(fn (string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Permohonan::STATUS_OPTIONS)
                    ->native(false),
                    
                Tables\Filters\SelectFilter::make('layanan_id')
                    ->relationship('layanan', 'name')
                    ->label('Jenis Layanan')
                    ->native(false),

                // FILTER BARU: Assignment Status
                Tables\Filters\SelectFilter::make('assignment_status')
                    ->label('Status Penugasan')
                    ->options([
                        'unassigned' => 'Belum Ditugaskan',
                        'assigned_to_me' => 'Ditugaskan ke Saya',
                        'assigned_to_others' => 'Ditugaskan ke Lainnya',
                        'overdue' => 'Assignment Overdue',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'unassigned' => $query->whereNull('assigned_to'),
                            'assigned_to_me' => $query->where('assigned_to', Auth::id()),
                            'assigned_to_others' => $query->whereNotNull('assigned_to')->where('assigned_to', '!=', Auth::id()),
                            'overdue' => $query->overdueAssignment(72),
                            default => $query,
                        };
                    }),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Ditugaskan Kepada')
                    ->options(fn () => User::role(['petugas', 'admin', 'kadis'])->pluck('name', 'id'))
                    ->native(false)
                    ->visible(fn () => Auth::user()->hasRole(['admin', 'kadis'])),
            ])
            ->actions([
                // ACTION BARU: Quick Assignment
                Tables\Actions\Action::make('quick_assign')
                    ->label('Ambil')
                    ->icon('heroicon-o-hand-raised')
                    ->color('primary')
                    ->action(function (Permohonan $record) {
                        $success = $record->assignTo(Auth::id());
                        
                        if ($success) {
                            // Update status if still new
                            if ($record->status === 'baru') {
                                $record->update([
                                    'status' => 'sedang_ditinjau',
                                    'catatan_petugas' => 'Permohonan telah diambil oleh ' . Auth::user()->name
                                ]);
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Tugas berhasil diambil')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (Permohonan $record) => $record->canBeAssignedTo())
                    ->requiresConfirmation(),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Permohonan $record) => 
                        Auth::user()->hasRole('admin') || 
                        $record->isAssignedTo(Auth::id())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // BULK ACTION BARU: Bulk Assignment
                    Tables\Actions\BulkAction::make('bulkAssign')
                        ->label('Tugaskan ke Petugas')
                        ->icon('heroicon-m-users')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Tugaskan Kepada')
                                ->options(fn () => User::role(['petugas', 'admin', 'kadis'])->pluck('name', 'id'))
                                ->required()
                                ->native(false)
                                ->searchable(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $successCount = 0;
                            $assigneeName = User::find($data['assigned_to'])->name;
                            
                            foreach ($records as $record) {
                                if ($record->assignTo($data['assigned_to'], Auth::id())) {
                                    $successCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Berhasil menugaskan {$successCount} permohonan")
                                ->body("Permohonan telah ditugaskan kepada {$assigneeName}")
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::user()->hasRole(['admin', 'kadis']))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulkAutoAssign')
                        ->label('Auto-Assign')
                        ->icon('heroicon-m-cpu-chip')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $successCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->isAssigned() && $record->autoAssign()) {
                                    $successCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Berhasil auto-assign {$successCount} permohonan")
                                ->body('Permohonan telah ditugaskan berdasarkan workload petugas')
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::user()->hasRole(['admin', 'kadis']))
                        ->requiresConfirmation()
                        ->modalDescription('Sistem akan secara otomatis menugaskan permohonan ke petugas dengan workload paling ringan.')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // Method yang sudah ada sebelumnya...
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
            'edit' => Pages\EditPermohonan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
    $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Untuk admin, kita panggil sebagai static method karena tidak terikat pada satu objek
         return static::getModel()::whereNull('assigned_to')->count();
     } else {
         // Untuk petugas, kita juga panggil sebagai static method
         return static::getModel()::where('assigned_to', $user->id)
             ->whereNotIn('status', ['selesai', 'ditolak'])
             ->count();
    }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getNavigationBadge();
        return $count > 0 ? 'warning' : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}