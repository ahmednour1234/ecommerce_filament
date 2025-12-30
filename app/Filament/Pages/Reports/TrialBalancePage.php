<?php

namespace App\Filament\Pages\Reports;

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

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.reports.trial-balance';

    public ?array $data = [];

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
                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\DatePicker::make('as_of_date')
                            ->label('As Of Date')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(Branch::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetTable()),

                        Forms\Components\Select::make('cost_center_id')
                            ->label('Cost Center')
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

        // Use Account model as base and wrap the union query in a subquery
        // This creates an Eloquent Builder that Filament can accept
        $query = Account::query()
            ->fromSub($unionQuery, 'trial_balance_data')
            ->select('trial_balance_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Account Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('debits')
                    ->label('Debits')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('credits')
                    ->label('Credits')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
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
            \Filament\Actions\Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Export logic would go here
                }),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('reports.trial_balance') ?? true;
    }
}

