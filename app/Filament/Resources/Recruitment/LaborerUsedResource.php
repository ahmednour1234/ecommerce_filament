<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\LaborerUsedResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\LaborerUsed;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaborerUsedResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = LaborerUsed::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'recruitment_contracts';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_contracts.used_laborers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('laborer_id')
                            ->label(tr('recruitment.fields.laborer', [], null, 'dashboard') ?: 'Laborer')
                            ->relationship('laborer', 'name_en')
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->name_en ?? '') . ' (' . ($record->passport_number ?? '') . ')')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (?LaborerUsed $record) => $record?->exists)
                            ->columnSpan(1),

                        Forms\Components\Select::make('agent_id')
                            ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                            ->relationship('agent', 'code')
                            ->options(Agent::query()->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('used_at')
                            ->label(tr('recruitment.fields.used_at', [], null, 'dashboard') ?: 'Used At')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->columnSpan(1),

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
                Tables\Columns\TextColumn::make('laborer.name_en')
                    ->label(tr('recruitment.fields.laborer', [], null, 'dashboard') ?: 'Laborer')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer 
                        ? ($record->laborer->name_en ?? '') . ' (' . ($record->laborer->passport_number ?? '') . ')'
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agent.code')
                    ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.nationality.name_' . app()->getLocale())
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer?->nationality 
                        ? (app()->getLocale() === 'ar' ? $record->laborer->nationality->name_ar : $record->laborer->nationality->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('laborer.profession.name_' . app()->getLocale())
                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->formatStateUsing(fn ($state, $record) => $record->laborer?->profession 
                        ? (app()->getLocale() === 'ar' ? $record->laborer->profession->name_ar : $record->laborer->profession->name_en)
                        : '')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_at')
                    ->label(tr('recruitment.fields.used_at', [], null, 'dashboard') ?: 'Used At')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('agent_id')
                    ->label(tr('recruitment.fields.agent', [], null, 'dashboard') ?: 'Agent')
                    ->relationship('agent', 'code')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->relationship('laborer.nationality', 'name_en')
                    ->options(Nationality::query()->where('is_active', true)->get()->mapWithKeys(function ($nationality) {
                        $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                        return [$nationality->id => $label];
                    }))
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('laborer', fn ($q) => $q->where('nationality_id', $value))
                        );
                    }),

                Tables\Filters\SelectFilter::make('profession_id')
                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                    ->relationship('laborer.profession', 'name_en')
                    ->options(Profession::query()->where('is_active', true)->get()->mapWithKeys(function ($profession) {
                        $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                        return [$profession->id => $label];
                    }))
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('laborer', fn ($q) => $q->where('profession_id', $value))
                        );
                    }),

                Tables\Filters\Filter::make('used_at')
                    ->label(tr('recruitment.fields.used_at', [], null, 'dashboard') ?: 'Used At')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('used_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('used_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete')
                    ->visible(fn () => auth()->user()?->can('recruitment.laborers_used.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment.laborers_used.delete') ?? false),
                ]),
            ])
            ->defaultSort('used_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaborersUsed::route('/'),
            'view' => Pages\ViewLaborerUsed::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['laborer.nationality', 'laborer.profession', 'agent']);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers_used.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers_used.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment.laborers_used.view') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment.laborers_used.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
