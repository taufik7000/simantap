<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\KnowledgeBaseResource\Pages;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategory; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = KnowledgeBase::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Pusat Bantuan';
    protected static ?string $navigationLabel = 'Pusat Bantuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('knowledge_base_category_id') // <-- Tambahkan field ini
                        ->label('Kategori')
                        ->options(KnowledgeBaseCategory::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->label('Judul Pertanyaan / Artikel')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(KnowledgeBase::class, 'slug', ignoreRecord: true),

                    Forms\Components\RichEditor::make('content')
                        ->label('Jawaban / Isi Dokumentasi')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),
                    
                    Forms\Components\Hidden::make('user_id')
                        ->default(auth()->id()),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('category.name') // <-- Tambahkan kolom ini
                    ->label('Kategori')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat oleh'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\TextColumn::make('updated_at')->label('Terakhir Update')->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('knowledge_base_category_id') // <-- Tambahkan filter ini
                    ->label('Filter Kategori')
                    ->options(KnowledgeBaseCategory::all()->pluck('name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBases::route('/'),
            'create' => Pages\CreateKnowledgeBase::route('/create'),
            'edit' => Pages\EditKnowledgeBase::route('/{record}/edit'),
        ];
    }    
}