<?php

namespace App\Filament\Resources\Housing;

use App\Filament\Resources\Housing\HousingStatusResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class HousingStatusResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = \App\Models\Housing\HousingStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'housing';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.status_management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('housing.status.management', [], null, 'dashboard') ?: 'إدارة الحالات')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label(tr('housing.status.key', [], null, 'dashboard') ?: 'مفتاح الحالة')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules([
                                'regex:/^[a-z0-9_]+$/',
                            ])
                            ->helperText(tr('housing.status.key', [], null, 'dashboard') ?: 'أحرف صغيرة وأرقام وشرطة سفلية فقط')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('housing.status.name_ar', [], null, 'dashboard') ?: 'الاسم بالعربية')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('housing.status.name_en', [], null, 'dashboard') ?: 'الاسم بالإنجليزية')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('color')
                            ->label(tr('housing.status.color', [], null, 'dashboard') ?: 'اللون')
                            ->options([
                                'primary' => 'Primary',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'danger' => 'Danger',
                                'info' => 'Info',
                                'gray' => 'Gray',
                            ])
                            ->default('primary')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('icon')
                            ->label(tr('housing.status.icon', [], null, 'dashboard') ?: 'الأيقونة')
                            ->helperText(tr('housing.status.icon', [], null, 'dashboard') ?: 'مثال: fa-info-circle')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('order')
                            ->label(tr('housing.status.order', [], null, 'dashboard') ?: 'الترتيب')
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('housing.status.active', [], null, 'dashboard') ?: 'نشط')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label(tr('housing.status.key', [], null, 'dashboard') ?: 'مفتاح الحالة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('housing.status.name_ar', [], null, 'dashboard') ?: 'الاسم بالعربية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('housing.status.name_en', [], null, 'dashboard') ?: 'الاسم بالإنجليزية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('color')
                    ->label(tr('housing.status.color', [], null, 'dashboard') ?: 'اللون')
                    ->color(fn ($state) => $state ?? 'gray')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('housing.status.active', [], null, 'dashboard') ?: 'نشط')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label(tr('housing.status.order', [], null, 'dashboard') ?: 'الترتيب')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('housing.status.active', [], null, 'dashboard') ?: 'نشط')
                    ->placeholder('الكل')
                    ->trueLabel('نشط فقط')
                    ->falseLabel('غير نشط فقط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListHousingStatuses::route('/'),
            'create' => Pages\CreateHousingStatus::route('/create'),
            'edit' => Pages\EditHousingStatus::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.statuses.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('housing.statuses.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('housing.statuses.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('housing.statuses.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
