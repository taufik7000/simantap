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
    protected static ?string $pluralModelLabel = 'Permohonan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // Bagian ini tidak perlu diubah
                        Forms\Components\Section::make('Informasi Permohonan')
                            ->schema([
                                Forms\Components\TextInput::make('kode_permohonan')->label('Kode Permohonan')->readOnly()->columnSpanFull(),
                                Forms\Components\Select::make('user_id')->relationship('user', 'name')->label('Diajukan Oleh (Warga)')->disabled()->columnSpanFull(),
                                Forms\Components\Select::make('layanan_id')->relationship('layanan', 'name')->label('Jenis Layanan')->disabled()->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('created_at')->label('Tanggal Pengajuan')->readOnly()->columnSpanFull(),
                            ])->columns(2),

                        // Bagian ini tidak perlu diubah
                        Forms\Components\Section::make('Informasi Penugasan')
                            ->schema([
                                Forms\Components\Select::make('assigned_to')->label('Ditugaskan Kepada')->options(fn() => User::role(['petugas', 'admin', 'kadis'])->pluck('name', 'id'))->searchable()->preload()->placeholder('Belum ditugaskan')->live()->afterStateUpdated(function ($state, Forms\Set $set) { if ($state) { $set('assigned_at', now()); $set('assigned_by', Auth::id()); } else { $set('assigned_at', null); $set('assigned_by', null); } }),
                                Forms\Components\DateTimePicker::make('assigned_at')->label('Tanggal Ditugaskan')->disabled(),
                                Forms\Components\Select::make('assigned_by')->label('Ditugaskan Oleh')->relationship('assignedBy', 'name')->disabled(),
                            ])->columns(3)->visible(fn() => Auth::user()->hasRole(['admin', 'kadis'])),
                        
                        // Bagian ini tidak perlu diubah, detail akan lebih baik dilihat di halaman View
                        Forms\Components\Section::make('Data Pemohon')->schema([ Forms\Components\KeyValue::make('data_pemohon')->label('Detail Data Pemohon')->disabled()->columnSpanFull()->hiddenOn('create'), ]),
                        Forms\Components\Section::make('Berkas Pemohon')->schema([ Forms\Components\Repeater::make('berkas_pemohon')->label('Daftar Berkas')->disabled()->collapsible()->schema([ Forms\Components\TextInput::make('name')->label('Nama Berkas'), Forms\Components\FileUpload::make('path_dokumen')->label('File')->disk('private')->downloadable(), ])->columnSpanFull()->hiddenOn('create'), ]),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Catatan Petugas')
                            ->schema([
                                // --- PERUBAHAN UTAMA DI SINI ---
                                Forms\Components\Select::make('status')
                                    ->label('Ubah Status Permohonan')
                                    // Menggunakan logika transisi dari model Permohonan
                                    ->options(fn(?Permohonan $record) => $record ? $record->getNextStatusOptions() : [])
                                    ->placeholder('Pilih status berikutnya...')
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Menggunakan catatan otomatis yang baru dan lebih detail
                                        $defaultMessages = [
                                            'proses_verifikasi' => 'Petugas sedang memeriksa kelengkapan dan keabsahan dokumen.',
                                            'proses_entri' => 'Berkas lengkap, permohonan diteruskan untuk proses entri data.',
                                            'entri_data_selesai' => 'Entri data selesai. Menunggu persetujuan dari pejabat berwenang.',
                                            'menunggu_persetujuan' => 'Permohonan sedang dalam antrean untuk ditinjau dan disetujui.',
                                            'disetujui' => 'Permohonan disetujui. Siap untuk penerbitan dokumen.',
                                            'dokumen_diterbitkan' => 'Dokumen resmi telah berhasil diterbitkan secara elektronik.',
                                            'proses_pengiriman' => 'Dokumen sedang dalam proses pengiriman ke lokasi pengambilan.',
                                            'selesai' => 'Proses telah selesai. Dokumen siap untuk diserahkan kepada pemohon.',
                                            'butuh_revisi' => 'PERHATIAN: Terdapat data/dokumen yang tidak sesuai. Jelaskan detail perbaikan pada catatan di bawah.',
                                            'ditolak' => 'MOHON MAAF: Permohonan ditolak. Alasan penolakan wajib diisi pada catatan.',
                                        ];
                                        $set('catatan_petugas', $defaultMessages[$state] ?? '');
                                    }),
                                
                                Forms\Components\Textarea::make('catatan_petugas')
                                    ->label('Catatan untuk Warga')
                                    ->placeholder('Tambahkan catatan atau instruksi yang jelas untuk warga...')
                                    ->rows(5)
                                    ->columnSpanFull()
                                    ->required(fn(Forms\Get $get) => in_array($get('status'), ['butuh_revisi', 'ditolak']))
                                    ->helperText(fn(Forms\Get $get) => in_array($get('status'), ['butuh_revisi', 'ditolak']) ? 'Catatan wajib diisi untuk status ini.' : 'Catatan ini akan dilihat oleh warga.'),
                            ]),
                        
                        // Bagian ini tidak perlu diubah
                        Forms\Components\Section::make('Statistik Workload')
                            ->schema([
                                Forms\Components\Placeholder::make('current_workload')->label('Workload Anda Saat Ini')->content(fn() => Permohonan::getPetugasWorkload(Auth::id()) . ' permohonan aktif'),
                                Forms\Components\Placeholder::make('total_unassigned')->label('Permohonan Belum Ditugaskan')->content(fn() => Permohonan::unassigned()->count() . ' permohonan'),
                                Forms\Components\Placeholder::make('overdue_assignments')->label('Assignment Overdue')->content(fn() => Permohonan::overdueAssignment(72)->count() . ' permohonan')->extraAttributes(['class' => 'text-red-600']),
                            ])
                            ->visible(fn() => Auth::user()->hasRole(['admin', 'kadis']))
                            ->collapsible(),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_permohonan')->label('Kode')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('user.name')->label('Warga')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('layanan.name')->label('Layanan')->searchable()->sortable()->wrap(),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Ditugaskan Ke')->default('Belum ditugaskan')->badge()->sortable(),
                
                // --- PERUBAHAN Tampilan Badge Status ---
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baru', 'dibatalkan' => 'gray',
                        'menunggu_verifikasi', 'proses_verifikasi' => 'info',
                        'proses_entri', 'entri_data_selesai' => 'warning',
                        'menunggu_persetujuan', 'proses_pengiriman' => 'primary',
                        'disetujui', 'dokumen_diterbitkan', 'selesai' => 'success',
                        'butuh_revisi', 'ditolak' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => Permohonan::STATUS_OPTIONS[$state] ?? $state)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Diupdate')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // --- PERUBAHAN Filter Status ---
                Tables\Filters\SelectFilter::make('status')
                    ->options(Permohonan::STATUS_OPTIONS)
                    ->native(false),
                
                // Filter lain tidak berubah
                Tables\Filters\SelectFilter::make('layanan_id')->relationship('layanan', 'name')->label('Jenis Layanan')->native(false),
                Tables\Filters\SelectFilter::make('assignment_status')->label('Status Penugasan')->options(['unassigned' => 'Belum Ditugaskan', 'assigned_to_me' => 'Ditugaskan ke Saya', 'assigned_to_others' => 'Ditugaskan ke Lainnya', 'overdue' => 'Assignment Overdue',])->query(function (Builder $query, array $data): Builder { if (!$data['value']) { return $query; } return match ($data['value']) { 'unassigned' => $query->whereNull('assigned_to'), 'assigned_to_me' => $query->where('assigned_to', Auth::id()), 'assigned_to_others' => $query->whereNotNull('assigned_to')->where('assigned_to', '!=', Auth::id()), 'overdue' => $query->overdueAssignment(72), default => $query, }; }),
                Tables\Filters\SelectFilter::make('assigned_to')->label('Ditugaskan Kepada')->options(fn() => User::role(['petugas', 'admin', 'kadis'])->pluck('name', 'id'))->native(false)->visible(fn() => Auth::user()->hasRole(['admin', 'kadis'])),
            ])
            ->actions([
                // Aksi lain tidak berubah
                Tables\Actions\Action::make('quick_assign')->label('Ambil')->icon('heroicon-o-hand-raised')->color('primary')->action(function (Permohonan $record) { if ($record->assignTo(Auth::id())) { if ($record->status === 'baru') { $record->update(['status' => 'menunggu_verifikasi', 'catatan_petugas' => 'Permohonan telah diambil oleh ' . Auth::user()->name]); } \Filament\Notifications\Notification::make()->title('Tugas berhasil diambil')->success()->send(); } })->visible(fn(Permohonan $record) => $record->canBeAssignedTo())->requiresConfirmation(),
                Tables\Actions\ViewAction::make(),
                // --- PERUBAHAN Logika Visibilitas Tombol Edit ---
                Tables\Actions\EditAction::make()->visible(fn(Permohonan $record) => (Auth::user()->hasRole('admin') || $record->isAssignedTo(Auth::id())) && !in_array($record->status, ['selesai', 'ditolak', 'dibatalkan'])),
            ])
            ->bulkActions([
                // Bulk Actions tidak berubah
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulkAssign')->label('Tugaskan ke Petugas')->icon('heroicon-m-users')->color('primary')->form([ Forms\Components\Select::make('assigned_to')->label('Tugaskan Kepada')->options(fn() => User::role(['petugas', 'admin', 'kadis'])->pluck('name', 'id'))->required()->native(false)->searchable(), ])->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void { $c=0; $n=User::find($data['assigned_to'])->name; foreach($records as $r){if($r->assignTo($data['assigned_to'],Auth::id()))$c++;} \Filament\Notifications\Notification::make()->title("Berhasil menugaskan {$c} permohonan")->body("Permohonan telah ditugaskan kepada {$n}")->success()->send(); })->visible(fn() => Auth::user()->hasRole(['admin', 'kadis']))->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulkAutoAssign')->label('Auto-Assign')->icon('heroicon-m-cpu-chip')->color('warning')->action(function (\Illuminate\Database\Eloquent\Collection $records): void { $c=0; foreach($records as $r){if(!$r->isAssigned()&&$r->autoAssign())$c++;} \Filament\Notifications\Notification::make()->title("Berhasil auto-assign {$c} permohonan")->body('Permohonan telah ditugaskan berdasarkan workload petugas')->success()->send(); })->visible(fn() => Auth::user()->hasRole(['admin', 'kadis']))->requiresConfirmation()->modalDescription('Sistem akan secara otomatis menugaskan permohonan ke petugas dengan workload paling ringan.')->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // Sisa kode tidak perlu diubah
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
        if (!$user) { return null; }
        if ($user->hasRole(['admin', 'kadis'])) {
            return static::getModel()::whereNull('assigned_to')->count();
        } else {
            return static::getModel()::where('assigned_to', $user->id)
                ->whereNotIn('status', ['selesai', 'ditolak', 'dibatalkan'])
                ->count();
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}