<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Branch;
use App\Services\Housing\HousingReportsService;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class RecruitmentHousingReportsPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.recruitment_housing.reports';
    protected static string $view = 'filament.pages.housing.reports';

    public $branch_id = null;
    public $from_date = null;
    public $to_date = null;

    public function getTitle(): string
    {
        return tr('housing.reports.title', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.reports.heading', [], null, 'dashboard') ?: 'تقارير الإيواء';
    }

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.reports', [], null, 'dashboard') ?: 'التقارير';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.reports.view') ?? false;
    }

    public function mount(): void
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
    }

    public function getStats(): array
    {
        $service = new HousingReportsService();
        $report = $service->getContractReport([
            'branch_id' => $this->branch_id,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);

        return $report['stats'] ?? [];
    }

    public function table(Table $table): Table
    {
        $service = new HousingReportsService();
        $report = $service->getContractReport([
            'branch_id' => $this->branch_id,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);

        $data = $report['data'] ?? [];

        return $table
            ->query(\Illuminate\Support\Facades\DB::table('laborers')->whereRaw('1 = 0'))
            ->columns([
                Tables\Columns\TextColumn::make('laborer_name')
                    ->label('اسم العامل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passport_number')
                    ->label('رقم الجواز')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الجوال'),
                Tables\Columns\TextColumn::make('branch')
                    ->label('الفرع'),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('عدد العقود'),
                Tables\Columns\TextColumn::make('active_contracts')
                    ->label('عقود نشطة'),
                Tables\Columns\TextColumn::make('completed_contracts')
                    ->label('عقود مكتملة'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('إجمالي مبلغ العقود')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('total_days')
                    ->label('إجمالي أيام العمل'),
            ])
            ->defaultSort('laborer_name', 'asc');
    }

    public function applyFilters(): void
    {
        $this->resetTable();
    }

    public function resetFilters(): void
    {
        $this->branch_id = null;
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
        $this->resetTable();
    }
}
