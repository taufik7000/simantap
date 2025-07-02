<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\WebsiteContentResource\Pages;
use App\Models\WebsiteContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Builder;

class WebsiteContentResource extends Resource
{
    protected static ?string $model = WebsiteContent::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'Kelola Website';
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Konten Website';
    protected static ?string $pluralModelLabel = 'Konten Website';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Website Content')
                    ->tabs([
                        Tabs\Tab::make('Informasi Dasar')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informasi Halaman')
                                    ->schema([
                                        Forms\Components\Select::make('page_key')
                                            ->label('Jenis Halaman')
                                            ->options(WebsiteContent::getPageTypes())
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if ($state) {
                                                    $pageTypes = WebsiteContent::getPageTypes();
                                                    $set('title', $pageTypes[$state] ?? '');
                                                }
                                            }),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul Halaman')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->maxLength(255)
                                            ->helperText('Biarkan kosong untuk auto-generate dari judul'),
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Publikasikan')
                                            ->default(true),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Urutan')
                                            ->numeric()
                                            ->default(0),
                                    ])->columns(2),

                                Section::make('SEO Meta')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(60)
                                            ->helperText('Maksimal 60 karakter'),
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(160)
                                            ->rows(3)
                                            ->helperText('Maksimal 160 karakter'),
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->image()
                                            ->directory('website-content')
                                            ->visibility('public'),
                                    ])->columns(1),
                            ]),

                        Tabs\Tab::make('Konten Halaman')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        // Konten untuk Homepage
                                        Section::make('Konten Beranda')
                                            ->schema([
                                                Forms\Components\TextInput::make('content.hero_title')
                                                    ->label('Judul Hero')
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('content.hero_subtitle')
                                                    ->label('Subtitle Hero')
                                                    ->rows(3),
                                                Forms\Components\TextInput::make('content.hero_cta_text')
                                                    ->label('Text Button Utama')
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('content.hero_secondary_cta')
                                                    ->label('Text Button Sekunder')
                                                    ->maxLength(50),
                                                
                                                // Features Section
                                                Repeater::make('content.features')
                                                    ->label('Fitur Unggulan')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title')
                                                            ->label('Judul Fitur')
                                                            ->required(),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Deskripsi')
                                                            ->rows(2),
                                                        Forms\Components\Select::make('icon')
                                                            ->label('Icon')
                                                            ->options([
                                                                'heroicon-o-bolt' => 'Lightning (Cepat)',
                                                                'heroicon-o-shield-check' => 'Shield (Aman)',
                                                                'heroicon-o-device-phone-mobile' => 'Mobile (Mudah)',
                                                                'heroicon-o-clock' => 'Clock (24/7)',
                                                                'heroicon-o-chart-bar' => 'Chart (Statistik)',
                                                                'heroicon-o-users' => 'Users (Komunitas)',
                                                            ])
                                                            ->searchable(),
                                                    ])
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                                    ->defaultItems(3)
                                                    ->maxItems(6),

                                                // Statistics Section
                                                Repeater::make('content.statistics')
                                                    ->label('Statistik')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('label')
                                                            ->label('Label')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('value')
                                                            ->label('Nilai')
                                                            ->required(),
                                                    ])
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '') . ' ' . ($state['label'] ?? ''))
                                                    ->defaultItems(4)
                                                    ->maxItems(8),
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('page_key') === 'homepage'),

                                        // Konten untuk About
                                        Section::make('Konten Tentang Kami')
                                            ->schema([
                                                Forms\Components\TextInput::make('content.intro_title')
                                                    ->label('Judul Intro')
                                                    ->maxLength(255),
                                                Forms\Components\RichEditor::make('content.intro_description')
                                                    ->label('Deskripsi Intro')
                                                    ->columnSpanFull(),
                                                Forms\Components\RichEditor::make('content.vision')
                                                    ->label('Visi')
                                                    ->columnSpanFull(),
                                                
                                                Repeater::make('content.mission')
                                                    ->label('Misi')
                                                    ->simple(
                                                        Forms\Components\Textarea::make('mission_item')
                                                            ->label('Item Misi')
                                                            ->rows(2)
                                                    )
                                                    ->defaultItems(3)
                                                    ->maxItems(10),

                                                Repeater::make('content.values')
                                                    ->label('Nilai-nilai')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title')
                                                            ->label('Judul Nilai')
                                                            ->required(),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Deskripsi')
                                                            ->rows(2),
                                                    ])
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                                    ->defaultItems(3)
                                                    ->maxItems(6),
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('page_key') === 'about'),

                                        // Konten untuk Contact
                                        Section::make('Konten Kontak')
                                            ->schema([
                                                Forms\Components\TextInput::make('content.intro_title')
                                                    ->label('Judul Intro')
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('content.intro_description')
                                                    ->label('Deskripsi Intro')
                                                    ->rows(3),
                                                Forms\Components\Textarea::make('content.office_address')
                                                    ->label('Alamat Kantor')
                                                    ->rows(3),
                                                Forms\Components\TextInput::make('content.phone')
                                                    ->label('Nomor Telepon')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('content.email')
                                                    ->label('Email')
                                                    ->email(),
                                                Forms\Components\TextInput::make('content.whatsapp')
                                                    ->label('WhatsApp')
                                                    ->tel(),
                                                
                                                Repeater::make('content.office_hours')
                                                    ->label('Jam Operasional')
                                                    ->simple(
                                                        Forms\Components\TextInput::make('hour')
                                                            ->label('Jam')
                                                            ->placeholder('Senin - Jumat: 08:00 - 16:00 WIB')
                                                    )
                                                    ->defaultItems(3)
                                                    ->maxItems(7),

                                                Repeater::make('content.social_media')
                                                    ->label('Media Sosial')
                                                    ->schema([
                                                        Forms\Components\Select::make('platform')
                                                            ->label('Platform')
                                                            ->options([
                                                                'Facebook' => 'Facebook',
                                                                'Instagram' => 'Instagram',
                                                                'Twitter' => 'Twitter',
                                                                'YouTube' => 'YouTube',
                                                                'LinkedIn' => 'LinkedIn',
                                                            ])
                                                            ->required(),
                                                        Forms\Components\TextInput::make('url')
                                                            ->label('URL')
                                                            ->url()
                                                            ->required(),
                                                    ])
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                                                    ->defaultItems(3)
                                                    ->maxItems(5),
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('page_key') === 'contact'),

                                        // Konten Custom untuk halaman lain
                                        Section::make('Konten Kustom')
                                            ->schema([
                                                KeyValue::make('content.custom_fields')
                                                    ->label('Field Kustom')
                                                    ->keyLabel('Nama Field')
                                                    ->valueLabel('Nilai')
                                                    ->reorderable()
                                                    ->addActionLabel('Tambah Field')
                                                    ->columnSpanFull(),
                                                    
                                                Forms\Components\RichEditor::make('content.custom_content')
                                                    ->label('Konten Bebas')
                                                    ->columnSpanFull(),
                                            ])
                                            ->visible(fn (Forms\Get $get) => !in_array($get('page_key'), ['homepage', 'about', 'contact'])),
                                    ])
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Halaman')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_key')
                    ->label('Jenis Halaman')
                    ->formatStateUsing(fn (string $state): string => WebsiteContent::getPageTypes()[$state] ?? $state)
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_key')
                    ->label('Jenis Halaman')
                    ->options(WebsiteContent::getPageTypes()),
                Tables\Filters\Filter::make('published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true))
                    ->label('Hanya yang Dipublikasikan'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (WebsiteContent $record) => route('website.page', $record->slug))
                    ->openUrlInNewTab()
                    ->color('info'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_publish')
                    ->label(fn (WebsiteContent $record) => $record->is_published ? 'Unpublish' : 'Publish')
                    ->icon(fn (WebsiteContent $record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (WebsiteContent $record) => $record->is_published ? 'warning' : 'success')
                    ->action(function (WebsiteContent $record) {
                        $record->update(['is_published' => !$record->is_published]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (WebsiteContent $record) => ($record->is_published ? 'Unpublish' : 'Publish') . ' Halaman')
                    ->modalDescription(fn (WebsiteContent $record) => 'Apakah Anda yakin ingin ' . ($record->is_published ? 'menyembunyikan' : 'mempublikasikan') . ' halaman ini?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_published' => true]);
                        }),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Sembunyikan')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_published' => false]);
                        }),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteContents::route('/'),
            'create' => Pages\CreateWebsiteContent::route('/create'),
            'edit' => Pages\EditWebsiteContent::route('/{record}/edit'),
        ];
    }
}