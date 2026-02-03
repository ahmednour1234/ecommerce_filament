<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\BankResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Bank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Bank::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.settings.banks';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(tr('forms.hr_banks.name.label', [], null, 'dashboard') ?: 'Name')
                        ->required()
                        ->maxLength(255),

                    /**
                     * NOTE:
                     * Column name is still `iban_prefix` in DB (as in your code),
                     * but we validate it as a FULL IBAN (example: SA7080000537608016041719).
                     * If you later rename the column to `iban`, update here & in the table columns.
                     */
                    Forms\Components\TextInput::make('iban_prefix')
                        ->label(tr('forms.hr_banks.iban_prefix.label', [], null, 'dashboard') ?: 'IBAN')
                        ->helperText(
                            tr('forms.hr_banks.iban_prefix.helper', [], null, 'dashboard')
                            ?: 'Enter full IBAN (e.g. SA7080000537608016041719). Spaces are allowed and will be removed.'
                        )
                        ->maxLength(34) // global IBAN max
                        ->nullable()
                        ->dehydrateStateUsing(function (?string $state) {
                            // remove spaces + uppercase
                            $state = $state ? strtoupper(preg_replace('/\s+/', '', $state)) : null;
                            return $state ?: null;
                        })
                        ->formatStateUsing(function (?string $state) {
                            // show without spaces but uppercase
                            return $state ? strtoupper(preg_replace('/\s+/', '', $state)) : null;
                        })
                        ->rule('regex:/^[A-Z]{2}[0-9A-Z]{13,32}$/') // generic IBAN shape
                        ->validationMessages([
                            'regex' => 'Invalid IBAN format. Example: SA7080000537608016041719',
                        ])
                        // Optional: Saudi-specific rule (enable if you ONLY accept SA)
                        ->rule('regex:/^SA[0-9]{22}$/')
                        ->validationMessages([
                            'regex' => 'Saudi IBAN must start with SA and contain 24 characters total (e.g. SA7080000537608016041719).',
                        ]),

                    Forms\Components\Toggle::make('active')
                        ->label(tr('forms.hr_banks.active.label', [], null, 'dashboard') ?: 'Active')
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
                    ->label(tr('tables.hr_banks.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('iban_prefix')
                    ->label(tr('tables.hr_banks.iban_prefix', [], null, 'dashboard') ?: 'IBAN')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state) => $state ? strtoupper(preg_replace('/\s+/', '', $state)) : null),

                Tables\Columns\IconColumn::make('active')
                    ->label(tr('tables.hr_banks.active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_banks.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.hr_banks.updated_at', [], null, 'dashboard') ?: 'Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label(tr('tables.hr_banks.filters.active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_banks.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_banks.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_banks.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_banks.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_banks.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_banks.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_banks.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_banks.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
