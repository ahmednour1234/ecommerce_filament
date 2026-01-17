<?php

namespace App\Filament\Resources\Recruitment\AgentResource\RelationManagers;

use App\Models\MainCore\Currency;
use App\Models\Recruitment\AgentLaborPrice;
use App\Models\Recruitment\Nationality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class AgentLaborPricesRelationManager extends RelationManager
{
    protected static string $relationship = 'laborPrices';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('general.actions.labor_prices', [], null, 'dashboard') ?: 'Labor Prices';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('agent_id')
                    ->label(tr('recruitment.prices.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->relationship('agent', 'code')
                    ->required()
                    ->disabled()
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

                Forms\Components\TextInput::make('profession_id')
                    ->label(tr('recruitment.prices.fields.profession', [], null, 'dashboard') ?: 'Profession ID')
                    ->numeric()
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
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('experience_level')
            ->columns([
                Tables\Columns\TextColumn::make('nationality.name_' . app()->getLocale())
                    ->label(tr('recruitment.prices.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->formatStateUsing(fn ($state, $record) => $record->nationality 
                        ? (app()->getLocale() === 'ar' ? $record->nationality->name_ar : $record->nationality->name_en)
                        : ($record->nationality_id ?? ''))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('profession_id')
                    ->label(tr('recruitment.prices.fields.profession', [], null, 'dashboard') ?: 'Profession ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('experience_level')
                    ->label(tr('recruitment.prices.fields.experience_level', [], null, 'dashboard') ?: 'Experience Level')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_amount')
                    ->label(tr('recruitment.prices.fields.cost_amount', [], null, 'dashboard') ?: 'Cost Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.name')
                    ->label(tr('recruitment.prices.fields.currency', [], null, 'dashboard') ?: 'Currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['currency', 'nationality']));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['agent_id'] = $this->ownerRecord->id;
        return $data;
    }
}
