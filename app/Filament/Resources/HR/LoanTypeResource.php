<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\LoanTypeResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\LoanType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanTypeResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = LoanType::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 40;
    protected static ?string $navigationTranslationKey = 'navigation.hr_loan_types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description_ar')
                            ->label(tr('fields.description_ar', [], null, 'dashboard') ?: 'Description (Arabic)')
                            ->rows(3)
                            ->nullable(),

                        Forms\Components\Textarea::make('description_en')
                            ->label(tr('fields.description_en', [], null, 'dashboard') ?: 'Description (English)')
                            ->rows(3)
                            ->nullable(),

                        Forms\Components\TextInput::make('max_amount')
                            ->label(tr('fields.max_amount', [], null, 'dashboard') ?: 'Max Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('max_installments')
                            ->label(tr('fields.max_installments', [], null, 'dashboard') ?: 'Max Installments')
                            ->numeric()
                            ->required()
                            ->integer()
                            ->minValue(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('fields.is_active', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_amount')
                    ->label(tr('fields.max_amount', [], null, 'dashboard') ?: 'Max Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_installments')
                    ->label(tr('fields.max_installments', [], null, 'dashboard') ?: 'Max Installments')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('fields.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('fields.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label(tr('fields.is_active', [], null, 'dashboard') ?: 'Active')
                    ->options([
                        true => tr('status.active', [], null, 'dashboard') ?: 'Active',
                        false => tr('status.inactive', [], null, 'dashboard') ?: 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.loan_types.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.loan_types.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr.loan_types.delete') ?? false),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanTypes::route('/'),
            'create' => Pages\CreateLoanType::route('/create'),
            'edit' => Pages\EditLoanType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.loan_types.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.loan_types.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr.loan_types.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr.loan_types.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr.loan_types.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
