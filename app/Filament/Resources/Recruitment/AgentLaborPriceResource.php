<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\AgentLaborPriceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\AgentLaborPrice;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class AgentLaborPriceResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = AgentLaborPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'الوكلاء';
    protected static ?string $navigationLabel = 'أسعار عمل الوكلاء';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('agent_id')
                            ->label(tr('recruitment.prices.fields.agent', [], null, 'dashboard') ?: 'Agent')
                            ->relationship('agent', 'code')
                            ->options(Agent::query()->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (?AgentLaborPrice $record, $operation) => $operation === 'edit' && $record?->exists)
                            ->dehydrated(),

                        Forms\Components\Select::make('nationality_id')
                            ->label(tr('recruitment.prices.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                            ->relationship('nationality', 'name_en')
                            ->options(Nationality::query()->where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                return [$nationality->id => $label . ($nationality->code ? ' (' . $nationality->code . ')' : '')];
                            }))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('profession_id')
                            ->label(tr('recruitment.prices.fields.profession', [], null, 'dashboard') ?: 'Profession')
                            ->relationship('profession', 'name_en')
                            ->options(Profession::query()->where('is_active', true)->get()->mapWithKeys(function ($profession) {
                                $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                return [$profession->id => $label . ($profession->code ? ' (' . $profession->code . ')' : '')];
                            }))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('experience_level')
                            ->label(tr('recruitment.prices.fields.experience_level', [], null, 'dashboard') ?: 'Experience Level')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('cost_amount')
                            ->label(tr('recruitment.prices.fields.cost_amount', [], null, 'dashboard') ?: 'Cost Amount')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Select::make('currency_id')
                            ->label(tr('recruitment.prices.fields.currency', [], null, 'dashboard') ?: 'Currency')
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment.fields.notes', [], null, 'dashboard') ?: 'Notes')
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
                Tables\Columns\TextColumn::make('agent.code')
                    ->label(tr('recruitment.prices.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality.name_' . app()->getLocale())
                    ->label(tr('recruitment.prices.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality 
                        ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en)
                        : ($record->nationality_id ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession.name_' . app()->getLocale())
                    ->label(tr('recruitment.prices.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->formatStateUsing(fn ($state, $record) => $record->profession 
                        ? (app()->getLocale() === 'ar' ? $record->profession->name_ar : $record->profession->name_en)
                        : ($record->profession_id ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('experience_level')
                    ->label(tr('recruitment.prices.fields.experience_level', [], null, 'dashboard') ?: 'Experience Level')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_amount')
                    ->label(tr('recruitment.prices.fields.cost_amount', [], null, 'dashboard') ?: 'Cost Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => ($record->currency?->symbol ?? '') . ' ' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('currency.name')
                    ->label(tr('recruitment.prices.fields.currency', [], null, 'dashboard') ?: 'Currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('agent_id')
                    ->label(tr('recruitment.prices.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->relationship('agent', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label(tr('recruitment.prices.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->relationship('nationality', 'name_en')
                    ->options(Nationality::query()->where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                        $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                        return [$nationality->id => $label];
                    }))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('profession_id')
                    ->label(tr('recruitment.prices.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->relationship('profession', 'name_en')
                    ->options(Profession::query()->where('is_active', true)->get()->mapWithKeys(function ($profession) {
                        $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                        return [$profession->id => $label];
                    }))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('experience_level')
                    ->options(fn () => AgentLaborPrice::query()->distinct()->pluck('experience_level', 'experience_level')->toArray())
                    ->label(tr('recruitment.prices.fields.experience_level', [], null, 'dashboard') ?: 'Experience Level'),

                Tables\Filters\SelectFilter::make('currency_id')
                    ->label(tr('recruitment.prices.fields.currency', [], null, 'dashboard') ?: 'Currency')
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('actions.view', [], null, 'dashboard') ?: 'View'),
                Tables\Actions\EditAction::make()
                    ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('recruitment.agent_prices.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('recruitment.agent_prices.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment.agent_prices.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['agent', 'currency']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgentLaborPrices::route('/'),
            'create' => Pages\CreateAgentLaborPrice::route('/create'),
            'view' => Pages\ViewAgentLaborPrice::route('/{record}'),
            'edit' => Pages\EditAgentLaborPrice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['agent', 'currency', 'nationality', 'profession']);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agent_prices.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agent_prices.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agent_prices.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.agent_prices.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment.agent_prices.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
