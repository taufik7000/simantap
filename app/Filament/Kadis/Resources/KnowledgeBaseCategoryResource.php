<?php
namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\KnowledgeBaseCategoryResource\Pages;
use App\Models\KnowledgeBaseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class KnowledgeBaseCategoryResource extends Resource
{
    protected static ?string $model = KnowledgeBaseCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Pusat Bantuan';
    protected static ?string $navigationLabel = 'Kategori Bantuan';
    protected static ?string $pluralModelLabel = 'Kategori Bantuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('knowledge_bases_count')->label('Jumlah Artikel')->counts('knowledgeBases'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBaseCategories::route('/'),
            'create' => Pages\CreateKnowledgeBaseCategory::route('/create'),
            'edit' => Pages\EditKnowledgeBaseCategory::route('/{record}/edit'),
        ];
    }    
}