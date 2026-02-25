<?php

namespace App\Filament\Resources\Housing\Rental;

use App\Filament\Resources\Housing\Rental\RentalHousingSalaryDeductionResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingSalaryDeduction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RentalHousingSalaryDeductionResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingSalaryDeduction::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.rental_housing.salary_deductions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laborer_id')
                    ->label(tr('forms.housing.deduction.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->relationship('laborer', 'name_ar')
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('deduction_date')
                    ->label(tr('forms.housing.deduction.date', [], null, 'dashboard') ?: 'تاريخ الخصم')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('deduction_type')
                    ->label(tr('forms.housing.deduction.type', [], null, 'dashboard') ?: 'نوع الخصم')
                    ->options([
                        'fine' => tr('forms.housing.deduction.type.fine', [], null, 'dashboard') ?: 'غرامة',
                        'advance' => tr('forms.housing.deduction.type.advance', [], null, 'dashboard') ?: 'سلفة',
                        'other' => tr('forms.housing.deduction.type.other', [], null, 'dashboard') ?: 'أخرى',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label(tr('forms.housing.deduction.amount', [], null, 'dashboard') ?: 'المبلغ')
                    ->numeric()
                    ->required()
                    ->prefix('SAR'),

                Forms\Components\Textarea::make('reason')
                    ->label(tr('forms.housing.deduction.reason', [], null, 'dashboard') ?: 'سبب الخصم')
                    ->rows(3)
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label(tr('forms.housing.deduction.notes', [], null, 'dashboard') ?: 'ملاحظات')
                    ->rows(3),

                Forms\Components\Select::make('status')
                    ->label(tr('forms.housing.deduction.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.deduction.status.pending', [], null, 'dashboard') ?: 'معلق',
                        'approved' => tr('housing.deduction.status.approved', [], null, 'dashboard') ?: 'معتمد',
                        'applied' => tr('housing.deduction.status.applied', [], null, 'dashboard') ?: 'مطبق',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('tables.housing.deduction.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deduction_date')
                    ->label(tr('tables.housing.deduction.date', [], null, 'dashboard') ?: 'تاريخ الخصم')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deduction_type')
                    ->label(tr('tables.housing.deduction.type', [], null, 'dashboard') ?: 'نوع الخصم')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.housing.deduction.amount', [], null, 'dashboard') ?: 'المبلغ')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.housing.deduction.status', [], null, 'dashboard') ?: 'الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'approved',
                        'success' => 'applied',
                    ]),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('deduction_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSalaryDeductions::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.salary_deductions.view_any') ?? false;
    }
}
