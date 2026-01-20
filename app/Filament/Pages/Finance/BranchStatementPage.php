<?php

namespace App\Filament\Pages\Finance;

use App\Filament\Concerns\ExportsTable;
use App\Filament\Concerns\FinanceModuleGate;
use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use App\Models\Finance\FinanceType;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TableExport;
use App\Exports\PdfExport;

class BranchStatementPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    use ExportsTable;
    use FinanceModuleGate;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.finance.branch-statement';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'branch_id' => null,
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'currency_id' => null,
            'kind' => null,
            'finance_type_id' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->options(Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\DatePicker::make('to')
                            ->label('To Date')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->options(Currency::where('is_active', true)->pluck('code', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('kind')
                            ->label('Kind (Optional)')
                            ->options([
                                'income' => 'Income',
                                'expense' => 'Expense',
                            ])
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('finance_type_id', null)),

                        Forms\Components\Select::make('finance_type_id')
                            ->label('Type (Optional)')
                            ->options(function ($get) {
                                $query = \App\Models\Finance\FinanceType::query()->where('is_active', true);
                                if ($get('kind')) {
                                    $query->where('kind', $get('kind'));
                                }
                                return $query->get()->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn ($get) => $get('kind') !== null),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function getTableQuery(): Builder
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['to']) || empty($data['currency_id'])) {
            return BranchTransaction::query()->whereRaw('1 = 0');
        }

        $query = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->whereBetween('trx_date', [$data['from'], $data['to']])
            ->with(['financeType', 'currency']);

        if (!empty($data['kind'])) {
            $query->whereHas('financeType', function ($q) use ($data) {
                $q->where('kind', $data['kind']);
            });
        }

        if (!empty($data['finance_type_id'])) {
            $query->where('finance_type_id', $data['finance_type_id']);
        }

        return $query->orderBy('trx_date')->orderBy('id');
    }

    public function table(Table $table): Table
    {
        $openingBalance = $this->getOpeningBalance();
        $runningBalance = $openingBalance;

        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('trx_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeType.kind')
                    ->label('Kind')
                    ->badge()
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ]),

                Tables\Columns\TextColumn::make('financeType.name_text')
                    ->label('Type')
                    ->getStateUsing(fn ($record) => $record->financeType?->name_text),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label('Reference')
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Recipient'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($state, $record) use (&$runningBalance) {
                        $amount = (float) $state;
                        if ($record->financeType?->kind === 'expense') {
                            $amount = -$amount;
                        }
                        $runningBalance += $amount;
                        return number_format($amount, 2);
                    })
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('running_balance')
                    ->label('Running Balance')
                    ->formatStateUsing(function ($state, $record) use (&$runningBalance) {
                        return number_format($runningBalance, 2);
                    })
                    ->alignEnd(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () use ($table) {
                        return $this->exportToExcel($table, $this->getExportFilename('xlsx'));
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () use ($table) {
                        return $this->exportToPdf($table, $this->getExportFilename('pdf'));
                    }),
            ])
            ->paginated(false);
    }

    protected function getOpeningBalance(): float
    {
        $data = $this->data;
        if (empty($data['branch_id']) || empty($data['from']) || empty($data['currency_id'])) {
            return 0;
        }

        $income = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'income'))
            ->sum('amount') ?? 0;

        $expense = BranchTransaction::query()
            ->where('branch_id', $data['branch_id'])
            ->where('currency_id', $data['currency_id'])
            ->where('trx_date', '<', $data['from'])
            ->whereHas('financeType', fn ($q) => $q->where('kind', 'expense'))
            ->sum('amount') ?? 0;

        return (float) $income - (float) $expense;
    }

    protected function getExportTitle(): ?string
    {
        $branch = Branch::find($this->data['branch_id'] ?? null);
        $currency = Currency::find($this->data['currency_id'] ?? null);
        $from = $this->data['from'] ?? '';
        $to = $this->data['to'] ?? '';

        return 'Branch Statement - ' . ($branch?->name ?? '') . ' (' . $from . ' to ' . $to . ') - ' . ($currency?->code ?? '');
    }

    protected function getExportFilename(string $extension = 'xlsx'): string
    {
        $branch = Branch::find($this->data['branch_id'] ?? null);
        $sanitized = preg_replace('/[^a-z0-9]+/i', '_', $branch?->name ?? 'branch_statement');
        return strtolower($sanitized) . '_' . date('Y-m-d_His') . '.' . $extension;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('finance.view_reports') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
