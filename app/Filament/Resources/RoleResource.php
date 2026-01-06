<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationTranslationKey = 'menu.system.roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.roles.name', [], null, 'dashboard') ?: 'Role Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('guard_name')
                    ->label(tr('forms.roles.guard', [], null, 'dashboard') ?: 'Guard')
                    ->default('web')
                    ->required()
                    ->maxLength(50),

                Forms\Components\CheckboxList::make('permissions')
                    ->label(tr('forms.roles.permissions', [], null, 'dashboard') ?: 'Permissions')
                    ->relationship('permissions', 'name')
                    ->options(Permission::query()->pluck('name', 'id'))
                    ->columns(2)
                    ->gridDirection('row')
                    ->searchable()
                    ->helperText(tr('forms.roles.permissions_helper', [], null, 'dashboard') ?: 'Select permissions assigned to this role. You can select all permissions at once using the checkbox.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.roles.role', [], null, 'dashboard') ?: 'Role')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label(tr('tables.roles.guard', [], null, 'dashboard') ?: 'Guard')
                    ->sortable(),

                Tables\Columns\TagsColumn::make('permissions.name')
                    ->label(tr('tables.roles.permissions', [], null, 'dashboard') ?: 'Permissions')
                    ->limit(3),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('roles.update') ?? false),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('roles.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('roles.delete') ?? false),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ممكن بعدين تعمل RelationManager لليوزرز اللي على الـ role
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    /* ================== صلاحيات Filament مبنية على Spatie ================== */

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('roles.view_any') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('roles.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('roles.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('roles.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('roles.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('roles.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
