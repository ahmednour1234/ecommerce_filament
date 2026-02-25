<?php

namespace App\Filament\Resources\Housing\Recruitment;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryBatchResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingSalaryBatch;
use App\Services\Housing\HousingSalaryGeneratorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecruitmentHousingSalaryBatchResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingSalaryBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.recruitment_housing.salary_batches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('month')
                    ->label(tr('forms.housing.salary_batch.month', [], null, 'dashboard') ?: 'الشهر')
                    ->placeholder('YYYY-MM')
                    ->helperText(tr('forms.housing.salary_batch.month_helper', [], null, 'dashboard') ?: 'مثال: 2026-02')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^\d{4}-\d{2}$/'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label(tr('tables.housing.salary_batch.month', [], null, 'dashboard') ?: 'الشهر')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_salaries')
                    ->label(tr('tables.housing.salary_batch.total_salaries', [], null, 'dashboard') ?: 'إجمالي الرواتب')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label(tr('tables.housing.salary_batch.total_paid', [], null, 'dashboard') ?: 'المدفوع')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_pending')
                    ->label(tr('tables.housing.salary_batch.total_pending', [], null, 'dashboard') ?: 'المعلق')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_deductions')
                    ->label(tr('tables.housing.salary_batch.total_deductions', [], null, 'dashboard') ?: 'إجمالي الخصومات')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label(tr('tables.housing.salary_batch.workers_count', [], null, 'dashboard') ?: 'عدد العمالة')
                    ->counts('items')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label(tr('actions.housing.view_details', [], null, 'dashboard') ?: 'عرض التفاصيل')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => Pages\ViewSalaryBatch::getUrl(['record' => $record])),

                Tables\Actions\Action::make('print')
                    ->label(tr('actions.print', [], null, 'dashboard') ?: 'طباعة')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => Pages\PrintSalaryBatch::getUrl(['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label(tr('actions.housing.generate_salaries', [], null, 'dashboard') ?: 'إنشاء رواتب لجميع العمالة')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('month')
                            ->label(tr('forms.housing.salary_batch.month', [], null, 'dashboard') ?: 'الشهر')
                            ->placeholder('YYYY-MM')
                            ->helperText(tr('forms.housing.salary_batch.month_helper', [], null, 'dashboard') ?: 'مثال: 2026-02')
                            ->required()
                            ->regex('/^\d{4}-\d{2}$/')
                            ->default(now()->format('Y-m')),
                    ])
                    ->action(function (array $data) {
                        $service = new HousingSalaryGeneratorService();
                        $service->generateBatchForMonth($data['month']);
                    }),
            ])
            ->defaultSort('month', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryBatches::route('/'),
            'view' => Pages\ViewSalaryBatch::route('/{record}'),
            'print' => Pages\PrintSalaryBatch::route('/{record}/print'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.salary_batches.view_any') ?? false;
    }
}
