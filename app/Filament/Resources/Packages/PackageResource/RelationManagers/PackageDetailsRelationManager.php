<?php

namespace App\Filament\Resources\Packages\PackageResource\RelationManagers;

use App\Models\MainCore\Country;
use App\Models\PackageDetail;
use App\Models\Recruitment\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Cache;

class PackageDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'packageDetails';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('pdf.title', [], null, 'packages');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'recruitment';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(tr('fields.code', [], null, 'packages'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('title')
                    ->label(tr('fields.title', [], null, 'packages'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Forms\Components\Select::make('country_id')
                    ->label(tr('fields.country', [], null, 'packages'))
                    ->options(function () {
                        return Cache::remember('packages.countries', 21600, function () {
                            return Country::where('is_active', true)
                                ->get()
                                ->pluck('name_text', 'id')
                                ->toArray();
                        });
                    })
                    ->searchable()
                    ->columnSpan(1),

                Forms\Components\Select::make('profession_id')
                    ->label(tr('fields.profession', [], null, 'packages'))
                    ->options(function () {
                        return Cache::remember('packages.professions', 21600, function () {
                            return Profession::where('is_active', true)
                                ->get()
                                ->mapWithKeys(function ($profession) {
                                    $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                    return [$profession->id => $label . ($profession->code ? ' (' . $profession->code . ')' : '')];
                                })
                                ->toArray();
                        });
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('direct_cost')
                    ->label(tr('fields.direct_cost', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateDetailTotals($set, $get))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('gov_cost')
                    ->label(tr('fields.gov_cost', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateDetailTotals($set, $get))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('external_cost')
                    ->label(tr('fields.external_cost', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateDetailTotals($set, $get))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('tax_percent')
                    ->label(tr('fields.tax_percent', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100)
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => static::calculateDetailTotals($set, $get))
                    ->columnSpan(1),

                Forms\Components\TextInput::make('tax_value')
                    ->label(tr('fields.tax_value', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('total_with_tax')
                    ->label(tr('fields.total_with_tax', [], null, 'packages'))
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected static function calculateDetailTotals(callable $set, callable $get): void
    {
        $directCost = (float) ($get('direct_cost') ?? 0);
        $govCost = (float) ($get('gov_cost') ?? 0);
        $externalCost = (float) ($get('external_cost') ?? 0);

        $subtotal = $directCost + $govCost + $externalCost;
        $taxPercent = (float) ($get('tax_percent') ?? 0);
        $taxValue = $subtotal * ($taxPercent / 100);
        $totalWithTax = $subtotal + $taxValue;

        $set('tax_value', round($taxValue, 2));
        $set('total_with_tax', round($totalWithTax, 2));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('fields.code', [], null, 'packages'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label(tr('fields.title', [], null, 'packages'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name_text')
                    ->label(tr('fields.country', [], null, 'packages'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('profession.name_' . app()->getLocale())
                    ->label(tr('fields.profession', [], null, 'packages'))
                    ->formatStateUsing(fn ($state, $record) => $record->profession 
                        ? (app()->getLocale() === 'ar' ? $record->profession->name_ar : $record->profession->name_en)
                        : ($record->profession_id ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('direct_cost')
                    ->label(tr('fields.direct_cost', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gov_cost')
                    ->label(tr('fields.gov_cost', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('external_cost')
                    ->label(tr('fields.external_cost', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_with_tax')
                    ->label(tr('fields.total_with_tax', [], null, 'packages'))
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['package_id'] = $this->ownerRecord->id;
                        return $data;
                    })
                    ->using(function (array $data): Model {
                        $data['package_id'] = $this->ownerRecord->id;
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['package_id'] = $this->ownerRecord->id;
        return $data;
    }
}
