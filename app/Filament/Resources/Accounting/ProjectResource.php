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

class ProjectResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 14;

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
                    ->label(trans_dash('accounting.project_code', 'Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('accounting.project_name', 'Project Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(trans_dash('accounting.start_date', 'Start Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(trans_dash('accounting.end_date', 'End Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('accounting.active', 'Active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_dash('accounting.created_at', 'Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('accounting.active', 'Active'))
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('projects.update') ?? false),
                Tables\Actions\DeleteAction::make()
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

