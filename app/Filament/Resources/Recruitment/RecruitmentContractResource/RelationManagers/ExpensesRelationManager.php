<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;

use App\Exports\PdfExport;
use App\Exports\TableExport;
use App\Models\Recruitment\RecruitmentContractFinanceLink;
use App\Services\Recruitment\RecruitmentContractFinanceGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('recruitment_contract.tabs.expenses', [], null, 'dashboard') ?: 'مصروفات العقد';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0.01),

                Forms\Components\TextInput::make('recipient_name')
                    ->label(tr('recruitment_contract.fields.recipient_name', [], null, 'dashboard') ?: 'Recipient Name')
                    ->maxLength(255),

                Forms\Components\TextInput::make('payment_method')
                    ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeTransaction.recipient_name')
                    ->label(tr('recruitment_contract.fields.recipient_name', [], null, 'dashboard') ?: 'Recipient Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('financeTransaction.payment_method')
                    ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->searchable(),

                Tables\Columns\TextColumn::make('financeTransaction.trx_date')
                    ->label(tr('recruitment_contract.fields.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeTransaction.reference_no')
                    ->label(tr('recruitment_contract.fields.reference_no', [], null, 'dashboard') ?: 'Reference No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(tr('actions.create', [], null, 'dashboard') ?: 'Create')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01),

                        Forms\Components\TextInput::make('recipient_name')
                            ->label(tr('recruitment_contract.fields.recipient_name', [], null, 'dashboard') ?: 'Recipient Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(2),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        $contract = $livewire->ownerRecord;
                        $financeGateway = app(RecruitmentContractFinanceGateway::class);
                        $financeTransactionId = $financeGateway->postExpense(
                            $contract,
                            $data['amount'],
                            [
                                'recipient_name' => $data['recipient_name'] ?? null,
                                'payment_method' => $data['payment_method'] ?? null,
                                'note' => $data['notes'] ?? null,
                            ]
                        );
                        
                        if (!$financeTransactionId) {
                            throw new \Exception('Failed to create expense transaction');
                        }
                        
                        $link = $contract->expenses()->where('finance_transaction_id', $financeTransactionId)->first();
                        if (!$link) {
                            throw new \Exception('Failed to retrieve created expense link');
                        }
                        
                        return $link;
                    })
                    ->successNotificationTitle(tr('notifications.created', [], null, 'dashboard') ?: 'Created successfully')
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
                Tables\Actions\Action::make('export_excel')
                    ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return $this->exportToExcel();
                    })
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
                Tables\Actions\Action::make('export_pdf')
                    ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function () {
                        return $this->exportToPdf();
                    })
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
                Tables\Actions\Action::make('print')
                    ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn () => $this->getPrintUrl())
                    ->openUrlInNewTab()
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->fillForm(function (RecruitmentContractFinanceLink $record): array {
                        $transaction = $record->financeTransaction;
                        return [
                            'amount' => $record->amount,
                            'recipient_name' => $transaction?->recipient_name,
                            'payment_method' => $transaction?->payment_method,
                            'notes' => $transaction?->notes,
                        ];
                    })
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01),

                        Forms\Components\TextInput::make('recipient_name')
                            ->label(tr('recruitment_contract.fields.recipient_name', [], null, 'dashboard') ?: 'Recipient Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(2),
                    ])
                    ->using(function (RecruitmentContractFinanceLink $record, array $data): Model {
                        $financeGateway = app(RecruitmentContractFinanceGateway::class);
                        $financeGateway->updateExpense(
                            $record,
                            $data['amount'],
                            [
                                'recipient_name' => $data['recipient_name'] ?? null,
                                'payment_method' => $data['payment_method'] ?? null,
                                'note' => $data['notes'] ?? null,
                            ]
                        );
                        return $record->fresh();
                    })
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(function () {
                        $user = auth()->user();
                        return $user?->hasRole('super_admin') || ($user?->can('recruitment_contracts.finance.manage') ?? false);
                    }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getTableDataForExport(): array
    {
        $records = $this->getTable()->getQuery()->get();
        $columns = $this->getTable()->getColumns();
        
        $headers = [];
        foreach ($columns as $column) {
            if (method_exists($column, 'isHidden') && $column->isHidden()) {
                continue;
            }
            $headers[] = $column->getLabel() ?? $column->getName();
        }
        
        $formattedData = $records->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $column) {
                if (method_exists($column, 'isHidden') && $column->isHidden()) {
                    continue;
                }
                $name = $column->getName();
                $label = $column->getLabel() ?? $name;
                $value = $this->getColumnValue($record, $name, $column);
                $row[$label] = $value;
            }
            return $row;
        });
        
        return [
            'data' => $formattedData,
            'headers' => $headers,
        ];
    }

    protected function getColumnValue($record, string $key, $column): mixed
    {
        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $value = $record;
            foreach ($parts as $part) {
                if (is_object($value) && isset($value->$part)) {
                    $value = $value->$part;
                } elseif (is_array($value) && isset($value[$part])) {
                    $value = $value[$part];
                } else {
                    return '';
                }
            }
            return $value ?? '';
        }
        
        return $record->$key ?? '';
    }

    public function exportToExcel()
    {
        $exportData = $this->getTableDataForExport();
        $title = static::getTitle($this->ownerRecord, static::class) . ' - ' . now()->format('Y-m-d');
        $filename = 'expenses_' . now()->format('Y-m-d_His') . '.xlsx';
        
        $export = new TableExport($exportData['data'], $exportData['headers'], $title);
        return Excel::download($export, $filename);
    }

    public function exportToPdf()
    {
        $exportData = $this->getTableDataForExport();
        $title = static::getTitle($this->ownerRecord, static::class);
        $filename = 'expenses_' . now()->format('Y-m-d_His') . '.pdf';
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'contract_no' => $this->ownerRecord->contract_no ?? '',
        ];
        
        $export = new PdfExport($exportData['data'], $exportData['headers'], $title, $metadata);
        return $export->download($filename);
    }

    public function getPrintUrl(): string
    {
        $exportData = $this->getTableDataForExport();
        $title = static::getTitle($this->ownerRecord, static::class);
        $metadata = [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'contract_no' => $this->ownerRecord->contract_no ?? '',
        ];
        
        session()->flash('print_data', [
            'title' => $title,
            'headers' => $exportData['headers'],
            'rows' => $exportData['data']->map(fn($row) => array_values($row))->toArray(),
            'metadata' => $metadata,
        ]);
        
        return route('filament.exports.print');
    }
}
