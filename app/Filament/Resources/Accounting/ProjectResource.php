<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\ProjectResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AccountingModuleGate;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class ProjectResource extends Resource
{
    use TranslatableNavigation,AccountingModuleGate;

    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.projects';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Project Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(trans_dash('accounting.project_code', 'Project Code'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique code for the project'),

                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('accounting.project_name', 'Project Name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('accounting.description', 'Description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(trans_dash('accounting.start_date', 'Start Date'))
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(trans_dash('accounting.end_date', 'End Date'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->after('start_date'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('accounting.active', 'Active'))
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
                    ->label(tr('tables.projects.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.projects.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.projects.start_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.projects.end_date', [], null, 'dashboard'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.projects.active', [], null, 'dashboard'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.projects.filters.active', [], null, 'dashboard'))
                    ->placeholder(tr('tables.projects.filters.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.projects.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.projects.filters.inactive_only', [], null, 'dashboard')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('projects.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('projects.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('projects.delete') ?? false),
                ]),
            ])
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('projects.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('projects.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('projects.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

