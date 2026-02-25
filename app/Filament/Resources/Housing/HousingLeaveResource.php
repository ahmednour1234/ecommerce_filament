<?php

namespace App\Filament\Resources\Housing;

use App\Filament\Resources\Housing\HousingLeaveResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingLeave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HousingLeaveResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingLeave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'قسم الإيواء';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.leaves';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('laborer_id')
                    ->label(tr('forms.housing.leave.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->relationship('laborer', 'name_ar')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('leave_type')
                    ->label(tr('forms.housing.leave.type', [], null, 'dashboard') ?: 'نوع الإجازة')
                    ->options([
                        'annual' => tr('forms.housing.leave.type.annual', [], null, 'dashboard') ?: 'سنوية',
                        'exit_return' => tr('forms.housing.leave.type.exit_return', [], null, 'dashboard') ?: 'خروج وعودة',
                        'sick' => tr('forms.housing.leave.type.sick', [], null, 'dashboard') ?: 'مرضية',
                        'other' => tr('forms.housing.leave.type.other', [], null, 'dashboard') ?: 'أخرى',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label(tr('forms.housing.leave.start_date', [], null, 'dashboard') ?: 'تاريخ البداية')
                    ->required()
                    ->reactive(),

                Forms\Components\TextInput::make('days')
                    ->label(tr('forms.housing.leave.days', [], null, 'dashboard') ?: 'المدة (أيام)')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($state && $get('start_date')) {
                            $endDate = \Carbon\Carbon::parse($get('start_date'))->addDays($state - 1);
                            $set('end_date', $endDate->format('Y-m-d'));
                        }
                    }),

                Forms\Components\DatePicker::make('end_date')
                    ->label(tr('forms.housing.leave.end_date', [], null, 'dashboard') ?: 'تاريخ النهاية')
                    ->required()
                    ->disabled(),

                Forms\Components\Textarea::make('reason')
                    ->label(tr('forms.housing.leave.reason', [], null, 'dashboard') ?: 'السبب')
                    ->rows(3),

                Forms\Components\Select::make('status')
                    ->label(tr('forms.housing.leave.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.leave.status.pending', [], null, 'dashboard') ?: 'معلق',
                        'approved' => tr('housing.leave.status.approved', [], null, 'dashboard') ?: 'موافق عليه',
                        'completed' => tr('housing.leave.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(HousingLeave::query()->with('laborer'))
            ->columns([
                Tables\Columns\TextColumn::make('laborer.name_ar')
                    ->label(tr('tables.housing.leave.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('leave_type')
                    ->label(tr('tables.housing.leave.type', [], null, 'dashboard') ?: 'نوع الإجازة')
                    ->badge(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.housing.leave.start_date', [], null, 'dashboard') ?: 'تاريخ البداية')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.housing.leave.end_date', [], null, 'dashboard') ?: 'تاريخ النهاية')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('days')
                    ->label(tr('tables.housing.leave.days', [], null, 'dashboard') ?: 'المدة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label(tr('tables.housing.leave.reason', [], null, 'dashboard') ?: 'السبب')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.housing.leave.status', [], null, 'dashboard') ?: 'الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'approved',
                        'success' => 'completed',
                    ]),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.housing.approve', [], null, 'dashboard') ?: 'موافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),

                Tables\Actions\Action::make('register_return')
                    ->label(tr('actions.housing.register_return', [], null, 'dashboard') ?: 'تسجيل العودة')
                    ->icon('heroicon-o-arrow-left')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'approved' && !$record->return_registered_at)
                    ->action(fn ($record) => $record->update([
                        'status' => 'completed',
                        'return_registered_at' => now(),
                    ])),

                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(tr('actions.housing.add_leave', [], null, 'dashboard') ?: 'إضافة إجازة جديدة'),

                Tables\Actions\Action::make('complete_ended')
                    ->label(tr('actions.housing.complete_ended_leaves', [], null, 'dashboard') ?: 'إكمال الإجازات المنتهية')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function () {
                        HousingLeave::where('status', 'approved')
                            ->where('end_date', '<', now())
                            ->whereNull('return_registered_at')
                            ->update(['status' => 'completed']);
                    }),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHousingLeaves::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.leaves.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
