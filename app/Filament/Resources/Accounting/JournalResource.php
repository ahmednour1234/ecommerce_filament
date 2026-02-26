<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Accounting\JournalResource\Pages;
use App\Models\Accounting\Journal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class JournalResource extends Resource
{
    use TranslatableNavigation,  AccountingModuleGate;

    protected static ?string $model = Journal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 3;

    // ✅ Fix: this resource is journals (not journal_entries)
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.journals';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(
                trans_dash('sections.basic_information', 'Basic Information')
            )
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label(trans_dash('forms.journals.code', 'Journal Code'))
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->helperText(trans_dash('forms.journals.code_helper', 'Unique code for the journal')),

                    Forms\Components\TextInput::make('name')
                        ->label(trans_dash('forms.journals.name', 'Journal Name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->label(trans_dash('forms.journals.type', 'Journal Type'))
                        ->options([
                            'general'  => trans_dash('journals.types.general', 'General Journal'),
                            'bank'     => trans_dash('journals.types.bank', 'Bank Journal'),
                            'cash'     => trans_dash('journals.types.cash', 'Cash Journal'),
                            'purchase' => trans_dash('journals.types.purchase', 'Purchase Journal'),
                            'sales'    => trans_dash('journals.types.sales', 'Sales Journal'),
                        ])
                        ->required()
                        ->default('general'),

                    Forms\Components\Toggle::make('is_active')
                        ->label(trans_dash('common.active', 'Active'))
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
                Tables\Columns\TextColumn::make('code')
                    ->label(trans_dash('tables.journals.code', 'Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('tables.journals.name', 'Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(trans_dash('tables.journals.type', 'Type'))
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'general'  => trans_dash('journals.types.general', 'General'),
                        'bank'     => trans_dash('journals.types.bank', 'Bank'),
                        'cash'     => trans_dash('journals.types.cash', 'Cash'),
                        'purchase' => trans_dash('journals.types.purchase', 'Purchase'),
                        'sales'    => trans_dash('journals.types.sales', 'Sales'),
                        default    => $state,
                    })
                    ->colors([
                        'primary' => 'general',
                        'success' => 'bank',
                        'warning' => 'cash',
                        'info'    => 'purchase',
                        'gray'    => 'sales',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('common.active', 'Active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('journal_entries_count')
                    ->label(trans_dash('tables.journals.entries', 'Entries'))
                    ->counts('journalEntries')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_dash('tables.common.created_at', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(trans_dash('filters.type', 'Type'))
                    ->options([
                        'general'  => trans_dash('journals.types.general', 'General Journal'),
                        'bank'     => trans_dash('journals.types.bank', 'Bank Journal'),
                        'cash'     => trans_dash('journals.types.cash', 'Cash Journal'),
                        'purchase' => trans_dash('journals.types.purchase', 'Purchase Journal'),
                        'sales'    => trans_dash('journals.types.sales', 'Sales Journal'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('common.active', 'Active'))
                    ->placeholder(trans_dash('common.all', 'All'))
                    ->trueLabel(trans_dash('common.active_only', 'Active only'))
                    ->falseLabel(trans_dash('common.inactive_only', 'Inactive only')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(trans_dash('common.view', 'View'))
                    ->visible(fn () => auth()->user()?->can('journals.view') ?? true),

                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('journals.update') ?? false),

                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('journals.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('journals.delete') ?? false),
                ]),
            ])
            ->defaultSort('code', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'view'   => Pages\ViewJournal::route('/{record}'),
            'edit'   => Pages\EditJournal::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return auth()->user()?->can('journals.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('journals.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('journals.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('journals.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('journals.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
