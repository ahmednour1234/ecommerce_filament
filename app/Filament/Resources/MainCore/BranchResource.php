<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\BranchResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'menu.settings.branches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique code for the branch'),

                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Branch')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Optional: Select a parent branch if this is a sub-branch'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->helperText('Additional flexible data (optional)'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Branch')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('branches.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('branches.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('branches.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('branches.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('branches.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

