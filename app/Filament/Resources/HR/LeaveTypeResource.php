<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\LeaveTypeResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\LeaveType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = LeaveType::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'أنواع الإجازات';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('forms.hr_leave_types.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('forms.hr_leave_types.name_en', [], null, 'dashboard') ?: 'Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('allowed_days_per_year')
                            ->label(tr('forms.hr_leave_types.allowed_days_per_year', [], null, 'dashboard') ?: 'Allowed Days Per Year')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(365)
                            ->default(0)
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('forms.hr_leave_types.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'active' => tr('status.active', [], null, 'dashboard') ?: 'Active',
                                'inactive' => tr('status.inactive', [], null, 'dashboard') ?: 'Inactive',
                            ])
                            ->required()
                            ->default('active')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description_ar')
                            ->label(tr('forms.hr_leave_types.description_ar', [], null, 'dashboard') ?: 'Description (Arabic)')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description_en')
                            ->label(tr('forms.hr_leave_types.description_en', [], null, 'dashboard') ?: 'Description (English)')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.hr_leave_types.name', [], null, 'dashboard') ?: 'Name')
                    ->getStateUsing(fn (LeaveType $record) => app()->getLocale() === 'ar' ? $record->name_ar : $record->name_en)
                    ->searchable(query: function ($query, $search) {
                        return $query->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('allowed_days_per_year')
                    ->label(tr('tables.hr_leave_types.allowed_days', [], null, 'dashboard') ?: 'Allowed Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.hr_leave_types.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => tr("status.{$state}", [], null, 'dashboard') ?: ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_leave_types.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.hr_leave_types.updated_at', [], null, 'dashboard') ?: 'Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.hr_leave_types.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'active' => tr('status.active', [], null, 'dashboard') ?: 'Active',
                        'inactive' => tr('status.inactive', [], null, 'dashboard') ?: 'Inactive',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.leave_types.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr.leave_types.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr.leave_types.delete') ?? false),
                ]),
            ])
            ->defaultSort('name_en');
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
            'index' => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit' => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.leave_types.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.leave_types.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr.leave_types.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr.leave_types.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr.leave_types.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

