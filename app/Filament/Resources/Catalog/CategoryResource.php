<?php

namespace App\Filament\Resources\Catalog;

use App\Filament\Resources\Catalog\CategoryResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\InventoryModuleGate;
use App\Models\Catalog\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use TranslatableNavigation, InventoryModuleGate;

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Products & Inventory';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'menu.products.categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('forms.categories.sections.basic_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('forms.categories.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label(trans_dash('forms.categories.slug.label'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('forms.categories.slug.helper_text')),

                        Forms\Components\Select::make('parent_id')
                            ->label(trans_dash('forms.categories.parent_id.label'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('forms.categories.description.label'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image')
                            ->label(trans_dash('forms.categories.image.label'))
                            ->image()
                            ->directory('categories')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(trans_dash('forms.categories.sort_order.label'))
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('forms.categories.is_active.label'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('tables.categories.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(trans_dash('tables.categories.parent'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label(trans_dash('tables.categories.image'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label(trans_dash('tables.categories.products'))
                    ->counts('products')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('tables.categories.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('filters.categories.is_active.label'))
                    ->placeholder(trans_dash('filters.categories.is_active.placeholder'))
                    ->trueLabel(trans_dash('filters.categories.is_active.true_label'))
                    ->falseLabel(trans_dash('filters.categories.is_active.false_label')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('categories.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('categories.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('categories.delete') ?? false),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('categories.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('categories.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('categories.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('categories.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

