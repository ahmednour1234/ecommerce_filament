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
    protected static ?string $navigationGroup = 'الفروع';
    protected static ?string $navigationLabel = 'الفروع';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.branches.code.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(tr('forms.branches.code.helper', [], null, 'dashboard')),

                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.branches.name.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('parent_id')
                            ->label(tr('forms.branches.parent_id.label', [], null, 'dashboard'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText(tr('forms.branches.parent_id.helper', [], null, 'dashboard')),

                        Forms\Components\Select::make('status')
                            ->label(tr('forms.branches.status.label', [], null, 'dashboard'))
                            ->options([
                                'active' => tr('forms.branches.status.options.active', [], null, 'dashboard'),
                                'inactive' => tr('forms.branches.status.options.inactive', [], null, 'dashboard'),
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label(tr('forms.branches.address.label', [], null, 'dashboard'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone')
                            ->label(tr('forms.branches.phone.label', [], null, 'dashboard'))
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('email')
                            ->label(tr('forms.branches.email.label', [], null, 'dashboard'))
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label(tr('forms.branches.metadata.label', [], null, 'dashboard'))
                            ->keyLabel(tr('forms.branches.metadata.key_label', [], null, 'dashboard'))
                            ->valueLabel(tr('forms.branches.metadata.value_label', [], null, 'dashboard'))
                            ->helperText(tr('forms.branches.metadata.helper', [], null, 'dashboard')),
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
                    ->label(tr('tables.branches.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.branches.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label(tr('tables.branches.parent_branch', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(tr('tables.branches.phone', [], null, 'dashboard'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email')
                    ->label(tr('tables.branches.email', [], null, 'dashboard'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.branches.status', [], null, 'dashboard'))
                    ->formatStateUsing(fn (string $state): string => tr('forms.branches.status.options.' . $state, [], null, 'dashboard'))
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label(tr('tables.branches.users', [], null, 'dashboard'))
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.branches.filters.status', [], null, 'dashboard'))
                    ->options([
                        'active' => tr('forms.branches.status.options.active', [], null, 'dashboard'),
                        'inactive' => tr('forms.branches.status.options.inactive', [], null, 'dashboard'),
                    ]),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(tr('tables.branches.filters.parent_branch', [], null, 'dashboard'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('branches.delete') ?? false),
                    Tables\Actions\ForceDeleteBulkAction::make()
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

