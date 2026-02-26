<?php

namespace App\Filament\Resources\Catalog;

use App\Filament\Resources\Catalog\BrandResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\InventoryModuleGate;
use App\Models\Catalog\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class BrandResource extends Resource
{
    use TranslatableNavigation, InventoryModuleGate;

    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Products & Inventory';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'menu.products.brands';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('forms.brands.sections.basic_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('forms.brands.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label(trans_dash('forms.brands.slug.label'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('forms.brands.slug.helper_text')),

                        Forms\Components\FileUpload::make('logo')
                            ->label(trans_dash('forms.brands.logo.label'))
                            ->image()
                            ->directory('brands')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('forms.brands.description.label'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('forms.brands.is_active.label'))
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
                    ->label(trans_dash('tables.brands.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('logo')
                    ->label(trans_dash('tables.brands.logo'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label(trans_dash('tables.brands.products'))
                    ->counts('products')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('tables.brands.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('filters.brands.is_active.label'))
                    ->placeholder(trans_dash('filters.brands.is_active.placeholder'))
                    ->trueLabel(trans_dash('filters.brands.is_active.true_label'))
                    ->falseLabel(trans_dash('filters.brands.is_active.false_label')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('brands.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('brands.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('brands.delete') ?? false),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('brands.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('brands.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('brands.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('brands.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

