<?php

namespace App\Filament\Pages\Reports;

use App\Services\Accounting\AccountingService;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class TrialBalancePage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
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

        // Build the query from trial balance data
        $subQueries = [];
        
        foreach ($trialBalance as $item) {
            $subQueries[] = \DB::table('accounts')
                ->where('id', $item['account']->id)
                ->selectRaw("
                    {$item['account']->id} as account_id,
                    '{$item['account']->code}' as code,
                    '{$item['account']->name}' as name,
                    '{$item['account']->type}' as type,
                    {$item['debits']} as debits,
                    {$item['credits']} as credits,
                    {$item['balance']} as balance
                ");
        }

        // Build union query
        $query = null;
        foreach ($subQueries as $subQuery) {
            if ($query === null) {
                $query = $subQuery;
            } else {
                $query->union($subQuery);
            }
        }

        // If no data, create empty query
        if ($query === null) {
            $query = \DB::table('accounts')->whereRaw('1 = 0');
        }

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
}

