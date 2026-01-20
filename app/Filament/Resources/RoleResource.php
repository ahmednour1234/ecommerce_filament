<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Helpers\PermissionHelper;
use App\Services\PermissionGrouper;
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
        $groupedPermissions = PermissionGrouper::getGroupedPermissions();
        $allPermissionOptions = static::getAllPermissionOptions();

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

                Forms\Components\TextInput::make('permission_search')
                    ->label(tr('forms.roles.permission_search', [], null, 'dashboard') ?: 'Search Permissions')
                    ->placeholder(tr('forms.roles.permission_search_placeholder', [], null, 'dashboard') ?: 'Search by permission name or label...')
                    ->reactive()
                    ->debounce(300)
                    ->dehydrated(false)
                    ->columnSpanFull(),

                Forms\Components\Tabs::make('permission_tabs')
                    ->tabs(static::buildPermissionTabs($groupedPermissions, $allPermissionOptions))
                    ->columnSpanFull()
                    ->reactive()
                    ->persistTabInQueryString(),
            ]);
    }

    protected static function getAllPermissionOptions(): array
    {
        $grouped = PermissionGrouper::getGroupedPermissions();
        $options = [];

        foreach ($grouped as $module => $permissions) {
            foreach ($permissions as $perm) {
                $label = PermissionHelper::getPermissionLabel($perm['name']);
                $options[$perm['id']] = $label;
            }
        }

        return $options;
    }

    protected static function buildPermissionTabs(array $groupedPermissions, array $allPermissionOptions): array
    {
        $tabs = [];

        foreach ($groupedPermissions as $module => $permissions) {
            $moduleLabel = PermissionHelper::getModuleLabel($module);
            $permissionIds = array_column($permissions, 'id');

            $tabs[] = Forms\Components\Tabs\Tab::make($module)
                ->label(fn ($get) => static::getTabLabel($module, $moduleLabel, $permissionIds, $get))
                ->visible(fn ($get) => static::shouldShowModule($module, $permissions, $get))
                ->badge(fn ($get) => static::getTabBadge($permissionIds, $get))
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('select_all_' . $module)
                                    ->label(tr('forms.roles.select_all', [], null, 'dashboard') ?: 'Select All')
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->action(function (callable $set, callable $get) use ($permissionIds) {
                                        $current = $get('permissions') ?? [];
                                        $new = array_unique(array_merge($current, $permissionIds));
                                        $set('permissions', array_values($new));
                                    }),

                                Forms\Components\Actions\Action::make('clear_' . $module)
                                    ->label(tr('forms.roles.clear', [], null, 'dashboard') ?: 'Clear')
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger')
                                    ->action(function (callable $set, callable $get) use ($permissionIds) {
                                        $current = $get('permissions') ?? [];
                                        $new = array_values(array_diff($current, $permissionIds));
                                        $set('permissions', $new);
                                    }),
                            ])
                                ->columnSpanFull(),

                            Forms\Components\CheckboxList::make('permissions')
                                ->label('')
                                ->options(function ($get) use ($module, $permissions, $allPermissionOptions) {
                                    $moduleOptions = [];
                                    foreach ($permissions as $perm) {
                                        if (isset($allPermissionOptions[$perm['id']])) {
                                            $moduleOptions[$perm['id']] = $allPermissionOptions[$perm['id']];
                                        }
                                    }
                                    return $moduleOptions;
                                })
                                ->columns(2)
                                ->gridDirection('row')
                                ->searchable(false)
                                ->reactive(),
                        ]),
                ]);
        }

        return $tabs;
    }

    protected static function getTabBadge(array $permissionIds, callable $get): ?string
    {
        $selected = $get('permissions') ?? [];
        $selectedCount = count(array_intersect($selected, $permissionIds));
        return $selectedCount > 0 ? (string) $selectedCount : null;
    }

    protected static function getTabLabel(string $module, string $moduleLabel, array $permissionIds, callable $get): string
    {
        $selected = $get('permissions') ?? [];
        $selectedCount = count(array_intersect($selected, $permissionIds));
        $totalCount = count($permissionIds);

        return "{$moduleLabel} ({$selectedCount}/{$totalCount})";
    }

    protected static function shouldShowModule(string $module, array $permissions, callable $get): bool
    {
        $search = strtolower($get('permission_search') ?? '');

        if (empty($search)) {
            return true;
        }

        $moduleLabel = strtolower(PermissionHelper::getModuleLabel($module));

        if (str_contains($moduleLabel, $search)) {
            return true;
        }

        foreach ($permissions as $perm) {
            $label = strtolower(PermissionHelper::getPermissionLabel($perm['name']));
            $key = strtolower($perm['name']);

            if (str_contains($label, $search) || str_contains($key, $search)) {
                return true;
            }
        }

        return false;
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
