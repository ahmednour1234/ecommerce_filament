<?php

namespace App\Filament\Pages\HR;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\Employee;
use App\Services\HR\EmployeeCommissionCalculator;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TableExport;

class EmployeeCommissionReport extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'حساب عمولات الموظفين';
    protected static ?int $navigationSort = 33;
    protected static string $view = 'filament.pages.hr.employee-commission-report';

    public ?int $employee_id = null;
    public ?string $date_from = null;
    public ?string $date_to = null;
    public array $results = [];
    public $total_contracts = 0;
    public $total_commission = 0;
    public $employee = null;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
    }

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.hr.commission_report', [], null, 'dashboard') ?: 'Commission Calculator';
    }

    public function getTitle(): string
    {
        return tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Calculator';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_employee_commission_report.view') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => !empty($this->results) && auth()->user()?->can('hr_employee_commission_report.export') ?? false)
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->visible(fn () => !empty($this->results) && auth()->user()?->can('hr_employee_commission_report.print') ?? false)
                ->url(fn () => route('filament.pages.hr.employee-commission-report-print', [
                    'employee_id' => $this->employee_id,
                    'date_from' => $this->date_from,
                    'date_to' => $this->date_to,
                ]) . '?' . http_build_query([
                    'employee_id' => $this->employee_id,
                    'date_from' => $this->date_from,
                    'date_to' => $this->date_to,
                ]))
                ->openUrlInNewTab(),
        ];
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(tr('pages.hr.commission_report.filters.employee', [], null, 'dashboard') ?: 'Employee')
                            ->options(Employee::active()->get()->mapWithKeys(fn ($emp) => [$emp->id => $emp->full_name]))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('pages.hr.commission_report.filters.date_from', [], null, 'dashboard') ?: 'From Date')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('pages.hr.commission_report.filters.date_to', [], null, 'dashboard') ?: 'To Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('search')
                                ->label(tr('actions.search', [], null, 'dashboard') ?: 'Search')
                                ->icon('heroicon-m-magnifying-glass')
                                ->color('primary')
                                ->action('calculate'),
                        ])->fullWidth(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function calculate(): void
    {
        $data = $this->form->getState();

        if (empty($data['employee_id']) || empty($data['date_from']) || empty($data['date_to'])) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Please fill all required fields')
                ->danger()
                ->send();
            return;
        }

        $calculator = app(EmployeeCommissionCalculator::class);
        $result = $calculator->calculate(
            $data['employee_id'],
            $data['date_from'],
            $data['date_to']
        );

        $this->employee_id = $data['employee_id'];
        $this->date_from = $data['date_from'];
        $this->date_to = $data['date_to'];
        $this->results = $result['results'];
        $this->total_contracts = $result['total_contracts'];
        $this->total_commission = $result['total_commission'];
        $this->employee = $result['employee'];
    }

    public function exportToExcel()
    {
        $headers = [
            tr('pages.hr.commission_report.table.commission', [], null, 'dashboard') ?: 'Commission',
            tr('pages.hr.commission_report.table.commission_type', [], null, 'dashboard') ?: 'Type',
            tr('pages.hr.commission_report.table.contract_count', [], null, 'dashboard') ?: 'Contract Count',
            tr('pages.hr.commission_report.table.tier_range', [], null, 'dashboard') ?: 'Tier Range',
            tr('pages.hr.commission_report.table.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract',
            tr('pages.hr.commission_report.table.total', [], null, 'dashboard') ?: 'Total',
        ];

        $data = collect($this->results)->map(function ($row) {
            return [
                $row['commission_name'],
                $row['commission_type'],
                $row['contract_count'],
                $row['tier_from'] . '-' . $row['tier_to'],
                number_format($row['amount_per_contract'], 2),
                number_format($row['total'], 2),
            ];
        });

        $title = tr('pages.hr.commission_report.title', [], null, 'dashboard') ?: 'Employee Commission Report';
        $export = new TableExport($data, $headers, $title);

        return Excel::download($export, 'employee_commission_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}
