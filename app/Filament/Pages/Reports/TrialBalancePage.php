<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Services\Accounting\AccountingService;
use App\Models\Accounting\Account;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;

class TrialBalancePage extends Page implements HasTable
{
    use InteractsWithTable;
    use ExportsTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.trial_balance';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.reports.trial-balance';

    public ?array $data = [];

    public function getTitle(): string
    {
        return tr('pages.reports.trial_balance.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.trial_balance.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'as_of_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('pages.reports.trial_balance.filters.section', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\DatePicker::make('as_of_date')
                            ->label(tr('pages.reports.trial_balance.filters.as_of_date', [], null, 'dashboard'))
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('pages.reports.trial_balance.filters.branch', [], null, 'dashboard'))
                            ->options(Branch::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('cost_center_id')
                            ->label(tr('pages.reports.trial_balance.filters.cost_center', [], null, 'dashboard'))
                            ->options(CostCenter::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $accountingService = app(AccountingService::class);
        $asOfDate = $this->data['as_of_date'] ?? now();
        $branchId = $this->data['branch_id'] ?? null;
        $costCenterId = $this->data['cost_center_id'] ?? null;

        $trialBalance = $accountingService->getTrialBalance(
            new \DateTime($asOfDate),
            $branchId,
            $costCenterId
        );

        // Build the union query from trial balance data
        $subQueries = [];
        
        foreach ($trialBalance as $item) {
            $account = $item['account'];
            $debits = (float) $item['debits'];
            $credits = (float) $item['credits'];
            $balance = (float) $item['balance'];
            
            // Create a subquery with literal values using a simple table reference
            $subQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0') // Never match any rows, we just need the structure
                ->selectRaw("
                    ? as account_id,
                    ? as code,
                    ? as name,
                    ? as type,
                    ? as debits,
                    ? as credits,
                    ? as balance
                ", [
                    $account->id,
                    $account->code,
                    $account->name,
                    $account->type,
                    $debits,
                    $credits,
                    $balance,
                ]);
        }

        // Build union query using Query Builder
        $unionQuery = null;
        foreach ($subQueries as $subQuery) {
            if ($unionQuery === null) {
                $unionQuery = $subQuery;
            } else {
                $unionQuery->union($subQuery);
            }
        }

        // If no data, create empty query
        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as account_id, NULL as code, NULL as name, NULL as type, 0 as debits, 0 as credits, 0 as balance');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => Account::query()
                ->fromSub($unionQuery, 'trial_balance_data')
                ->select('trial_balance_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('pages.reports.trial_balance.columns.account_code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('pages.reports.trial_balance.columns.account_name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('pages.reports.trial_balance.columns.type', [], null, 'dashboard'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('debits')
                    ->label(tr('pages.reports.trial_balance.columns.debits', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('credits')
                    ->label(tr('pages.reports.trial_balance.columns.credits', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label(tr('pages.reports.trial_balance.columns.balance', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable()
                    ->color(fn ($record) => $record->balance < 0 ? 'danger' : 'success'),
            ])
            ->defaultSort('code', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return $this->exportToExcel(null, $this->getExportFilename('xlsx'));
                }),

            \Filament\Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard'))
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    return $this->exportToPdf(null, $this->getExportFilename('pdf'));
                }),

            \Filament\Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard'))
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        $asOfDate = $this->data['as_of_date'] ?? now();
        $dateFormatted = \Carbon\Carbon::parse($asOfDate)->format('Y-m-d');
        return tr('pages.reports.trial_balance.export_title', ['date' => $dateFormatted], null, 'dashboard');
    }

    protected function getExportMetadata(): array
    {
        // Get base metadata from trait
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
        ];
        
        $metadata['as_of_date'] = $this->data['as_of_date'] ?? '';
        
        if (isset($this->data['branch_id'])) {
            $branch = Branch::find($this->data['branch_id']);
            $metadata['branch'] = $branch?->name ?? '';
        }
        
        if (isset($this->data['cost_center_id'])) {
            $costCenter = CostCenter::find($this->data['cost_center_id']);
            $metadata['cost_center'] = $costCenter?->name ?? '';
        }
        
        return $metadata;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('reports.trial_balance') ?? true;
    }
}

