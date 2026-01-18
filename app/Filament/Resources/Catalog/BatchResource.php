<?php

namespace App\Filament\Resources\Catalog;

use App\Filament\Resources\Catalog\BatchResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\InventoryModuleGate;
use App\Models\Catalog\Batch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BatchResource extends Resource
{
    use TranslatableNavigation, InventoryModuleGate;

    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Products & Inventory';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationTranslationKey = 'menu.products.batches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('forms.batches.sections.batch_information'))
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label(trans_dash('forms.batches.product_id.label'))
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('warehouse_id')
                            ->label(trans_dash('forms.batches.warehouse_id.label'))
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('batch_number')
                            ->label(trans_dash('forms.batches.batch_number.label'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\DatePicker::make('manufacturing_date')
                            ->label(trans_dash('forms.batches.manufacturing_date.label'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->nullable(),

                        Forms\Components\DatePicker::make('expiry_date')
                            ->label(trans_dash('forms.batches.expiry_date.label'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->nullable()
                            ->after('manufacturing_date'),

                        Forms\Components\TextInput::make('quantity')
                            ->label(trans_dash('forms.batches.quantity.label'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\TextInput::make('cost')
                            ->label(trans_dash('forms.batches.cost.label'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$')
                            ->minValue(0),

                        Forms\Components\TextInput::make('supplier_reference')
                            ->label(trans_dash('forms.batches.supplier_reference.label'))
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\Textarea::make('notes')
                            ->label(trans_dash('forms.batches.notes.label'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')
                    ->label(trans_dash('tables.batches.batch_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label(trans_dash('tables.batches.product'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label(trans_dash('tables.batches.warehouse'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('manufacturing_date')
                    ->label(trans_dash('tables.batches.manufacturing_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label(trans_dash('tables.batches.expiry_date'))
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : ($record->isExpiringSoon() ? 'warning' : null))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(trans_dash('tables.batches.quantity'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label(trans_dash('tables.batches.cost'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_dash('tables.batches.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label(trans_dash('filters.batches.product_id.label'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label(trans_dash('filters.batches.warehouse_id.label'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expired')
                    ->label(trans_dash('filters.batches.expired.label'))
                    ->query(fn ($query) => $query->where('expiry_date', '<', now())),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label(trans_dash('filters.batches.expiring_soon.label'))
                    ->query(fn ($query) => $query->whereBetween('expiry_date', [now(), now()->addDays(30)])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('batches.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('batches.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('batches.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('batches.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('batches.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('batches.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('batches.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

