<?php

namespace App\Filament\Resources\CompanyVisas\CompanyVisaContractResource\Pages;

use App\Filament\Resources\CompanyVisas\CompanyVisaContractResource;
use Modules\CompanyVisas\Entities\CompanyVisaContract;
use Modules\CompanyVisas\Entities\CompanyVisaContractExpense;
use Modules\CompanyVisas\Entities\CompanyVisaContractCost;
use Modules\CompanyVisas\Entities\CompanyVisaContractDocument;
use App\Models\Recruitment\Laborer;
use App\Models\Accounting\Account;
use App\Models\MainCore\PaymentMethod;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViewContract extends ViewRecord
{
    protected static string $resource = CompanyVisaContractResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('contract_tabs')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('details')
                            ->label(tr('company_visas.tabs.details', [], null, 'dashboard') ?: 'تفاصيل العقد')
                            ->schema([
                                Infolists\Components\Section::make(tr('company_visas.sections.contract_info', [], null, 'dashboard') ?: 'معلومات العقد الأساسية')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('contract_no')
                                            ->label(tr('company_visas.fields.contract_no', [], null, 'dashboard') ?: 'رقم العقد'),
                                        Infolists\Components\TextEntry::make('visaRequest.code')
                                            ->label(tr('company_visas.fields.visa_request', [], null, 'dashboard') ?: 'طلب التأشيرة'),
                                        Infolists\Components\TextEntry::make('agent.code')
                                            ->label(tr('company_visas.fields.agent', [], null, 'dashboard') ?: 'الوكيل'),
                                        Infolists\Components\TextEntry::make('contract_date')
                                            ->label(tr('company_visas.fields.contract_date', [], null, 'dashboard') ?: 'تاريخ العقد')
                                            ->date(),
                                        Infolists\Components\TextEntry::make('status')
                                            ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                                            ->badge()
                                            ->color(fn ($record) => $record->status_color),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(tr('company_visas.sections.workers_info', [], null, 'dashboard') ?: 'معلومات العمالة')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('workers_required')
                                            ->label(tr('company_visas.fields.workers_required', [], null, 'dashboard') ?: 'عدد العمالة المطلوبة')),
                                        Infolists\Components\TextEntry::make('linked_workers_count')
                                            ->label(tr('company_visas.fields.linked_workers_count', [], null, 'dashboard') ?: 'عدد العمالة المرتبطة')),
                                    ])
                                    ->columns(2),
                            ]),

                        Infolists\Components\Tabs\Tab::make('workers')
                            ->label(fn () => tr('company_visas.tabs.workers', [], null, 'dashboard') . ' (' . $this->record->linked_workers_count . ')')
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('workers_list')
                                            ->label('')
                                            ->formatStateUsing(function () {
                                                $workers = $this->record->workers()->with('pivot')->get();
                                                if ($workers->isEmpty()) {
                                                    return tr('common.no_data', [], null, 'dashboard') ?: 'لا توجد بيانات';
                                                }
                                                return $workers->map(function ($worker) {
                                                    $name = app()->getLocale() === 'ar' ? $worker->name_ar : $worker->name_en;
                                                    $cost = $worker->pivot->cost_per_worker ?? 0;
                                                    return "{$name} ({$worker->passport_number}) - " . number_format($cost, 2) . ' SAR';
                                                })->join("\n");
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('expenses')
                            ->label(fn () => tr('company_visas.tabs.expenses', [], null, 'dashboard') . ' (' . $this->record->expenses()->count() . ')')
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('expenses_list')
                                            ->label('')
                                            ->formatStateUsing(function () {
                                                $expenses = $this->record->expenses()->with(['expenseAccount', 'paymentMethod'])->get();
                                                if ($expenses->isEmpty()) {
                                                    return tr('common.no_data', [], null, 'dashboard') ?: 'لا توجد بيانات';
                                                }
                                                return $expenses->map(function ($expense) {
                                                    $account = $expense->expenseAccount?->name ?? 'N/A';
                                                    $amount = number_format($expense->amount, 2);
                                                    $date = $expense->expense_date->format('Y-m-d');
                                                    $method = $expense->paymentMethod?->name ?? 'N/A';
                                                    $invoice = $expense->invoice_no ?? 'N/A';
                                                    return "{$account} - {$amount} SAR - {$date} - {$method} - {$invoice}";
                                                })->join("\n");
                                            })
                                            ->columnSpanFull(),
                                        Infolists\Components\TextEntry::make('expenses_total')
                                            ->label(tr('company_visas.fields.total', [], null, 'dashboard') ?: 'الإجمالي')
                                            ->formatStateUsing(fn () => number_format($this->record->expenses()->sum('amount'), 2) . ' SAR')
                                            ->weight(FontWeight::Bold),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('costs')
                            ->label(fn () => tr('company_visas.tabs.costs', [], null, 'dashboard') . ' (' . $this->record->costs()->count() . ')')
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('costs_list')
                                            ->label('')
                                            ->formatStateUsing(function () {
                                                $costs = $this->record->costs()->get();
                                                if ($costs->isEmpty()) {
                                                    return tr('common.no_data', [], null, 'dashboard') ?: 'لا توجد بيانات';
                                                }
                                                return $costs->map(function ($cost) {
                                                    $perWorker = number_format($cost->cost_per_worker, 2);
                                                    $total = number_format($cost->total_cost, 2);
                                                    $dueDate = $cost->due_date->format('Y-m-d');
                                                    $desc = $cost->description ?? '';
                                                    return "تكلفة العامل: {$perWorker} SAR - الإجمالي: {$total} SAR - الاستحقاق: {$dueDate} - {$desc}";
                                                })->join("\n");
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('documents')
                            ->label(fn () => tr('company_visas.tabs.documents', [], null, 'dashboard') . ' (' . $this->record->documents()->count() . ')')
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\TextEntry::make('documents_list')
                                            ->label('')
                                            ->formatStateUsing(function () {
                                                $documents = $this->record->documents()->get();
                                                if ($documents->isEmpty()) {
                                                    return tr('common.no_data', [], null, 'dashboard') ?: 'لا توجد بيانات';
                                                }
                                                return $documents->map(function ($doc) {
                                                    $url = Storage::url($doc->file_path);
                                                    $date = $doc->created_at->format('Y-m-d H:i');
                                                    return "<a href='{$url}' target='_blank'>{$doc->title}</a> - {$date}";
                                                })->join("\n");
                                            })
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getLinkWorkersForm(): array
    {
        $contract = $this->record;
        return [
            Forms\Components\TextInput::make('agent')
                ->label(tr('company_visas.fields.agent', [], null, 'dashboard') ?: 'الوكيل')
                ->default($contract->agent?->code)
                ->disabled(),
            Forms\Components\TextInput::make('profession')
                ->label(tr('company_visas.fields.profession', [], null, 'dashboard') ?: 'المهنة')
                ->default($contract->profession?->name_ar ?? $contract->profession?->name_en)
                ->disabled(),
            Forms\Components\TextInput::make('country')
                ->label(tr('company_visas.fields.country', [], null, 'dashboard') ?: 'الدولة')
                ->default($contract->country?->name_text)
                ->disabled(),
            Forms\Components\Select::make('worker_ids')
                ->label(tr('company_visas.fields.workers', [], null, 'dashboard') ?: 'العمالة المتاحة')
                ->multiple()
                ->options(function () use ($contract) {
                    $linkedIds = $contract->workers()->pluck('worker_id')->toArray();
                    return Laborer::where('agent_id', $contract->agent_id)
                        ->where('profession_id', $contract->profession_id)
                        ->where('country_id', $contract->country_id)
                        ->where('is_available', true)
                        ->whereNotIn('id', $linkedIds)
                        ->get()
                        ->mapWithKeys(function ($worker) {
                            $name = app()->getLocale() === 'ar' ? $worker->name_ar : $worker->name_en;
                            return [$worker->id => $name . ' (' . $worker->passport_number . ')'];
                        })->toArray();
                })
                ->searchable()
                ->required(),
        ];
    }

    protected function getAddExpenseForm(): array
    {
        return [
            Forms\Components\Select::make('expense_account_id')
                ->label(tr('company_visas.fields.expense_account', [], null, 'dashboard') ?: 'حساب المصروف')
                ->options(Account::active()->get()->mapWithKeys(function ($account) {
                    return [$account->id => $account->code . ' - ' . $account->name];
                })->toArray())
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('amount')
                ->label(tr('company_visas.fields.amount', [], null, 'dashboard') ?: 'المبلغ')
                ->required()
                ->numeric()
                ->minValue(0),
            Forms\Components\Toggle::make('includes_vat')
                ->label(tr('company_visas.fields.includes_vat', [], null, 'dashboard') ?: 'يشمل ضريبة القيمة المضافة')
                ->default(false),
            Forms\Components\DatePicker::make('expense_date')
                ->label(tr('company_visas.fields.expense_date', [], null, 'dashboard') ?: 'تاريخ المصروف')
                ->required()
                ->default(now()),
            Forms\Components\Select::make('payment_method_id')
                ->label(tr('company_visas.fields.payment_method', [], null, 'dashboard') ?: 'طريقة الدفع')
                ->options(PaymentMethod::where('is_active', true)->get()->pluck('name', 'id')->toArray())
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('invoice_no')
                ->label(tr('company_visas.fields.invoice_no', [], null, 'dashboard') ?: 'رقم الفاتورة')
                ->maxLength(255),
            Forms\Components\FileUpload::make('attachment')
                ->label(tr('company_visas.fields.attachment', [], null, 'dashboard') ?: 'المرفق')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif'])
                ->directory('company-visas/expenses')
                ->visibility('private'),
            Forms\Components\Textarea::make('description')
                ->label(tr('company_visas.fields.description', [], null, 'dashboard') ?: 'الوصف')
                ->rows(3),
        ];
    }

    protected function getUpdateStatusForm(): array
    {
        return [
            Forms\Components\Select::make('status')
                ->label(tr('company_visas.fields.status', [], null, 'dashboard') ?: 'الحالة')
                ->options([
                    'draft' => tr('company_visas.status.draft', [], null, 'dashboard') ?: 'مسودة',
                    'active' => tr('company_visas.status.active', [], null, 'dashboard') ?: 'نشط',
                    'completed' => tr('company_visas.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                    'cancelled' => tr('company_visas.status.cancelled', [], null, 'dashboard') ?: 'ملغي',
                ])
                ->required()
                ->default(fn () => $this->record->status),
            Forms\Components\Textarea::make('notes')
                ->label(tr('company_visas.fields.notes', [], null, 'dashboard') ?: 'ملاحظات')
                ->rows(3),
        ];
    }

    protected function getContractCostForm(): array
    {
        $contract = $this->record;
        return [
            Forms\Components\TextInput::make('cost_per_worker')
                ->label(tr('company_visas.fields.cost_per_worker', [], null, 'dashboard') ?: 'تكلفة العامل الواحد (ريال)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->reactive()
                ->afterStateUpdated(function ($state, Forms\Set $set) use ($contract) {
                    $set('total_cost', $state * $contract->linked_workers_count);
                }),
            Forms\Components\TextInput::make('total_cost')
                ->label(tr('company_visas.fields.total_cost', [], null, 'dashboard') ?: 'التكلفة الإجمالية')
                ->disabled()
                ->dehydrated()
                ->default(fn (Forms\Get $get) => $get('cost_per_worker') * $contract->linked_workers_count),
            Forms\Components\DatePicker::make('due_date')
                ->label(tr('company_visas.fields.due_date', [], null, 'dashboard') ?: 'الاستحقاق')
                ->required()
                ->default(now()->addDays(30)),
            Forms\Components\Textarea::make('description')
                ->label(tr('company_visas.fields.description', [], null, 'dashboard') ?: 'الوصف')
                ->rows(3),
            Forms\Components\Placeholder::make('info')
                ->label('')
                ->content(tr('company_visas.messages.finance_entry_info', [], null, 'dashboard')
                    ?: 'سيتم إنشاء قيد محاسبي تلقائياً: حساب المشترى (مدين) وحساب الوكيل (دائن).'),
        ];
    }

    protected function linkWorkers(array $data): void
    {
        DB::transaction(function () use ($data) {
            \Modules\CompanyVisas\Services\CompanyVisaContractService::linkWorkers(
                $this->record,
                $data['worker_ids']
            );
        });
        $this->dispatch('refresh');
    }

    protected function addExpense(array $data): void
    {
        DB::transaction(function () use ($data) {
            $expense = CompanyVisaContractExpense::create([
                'contract_id' => $this->record->id,
                'expense_account_id' => $data['expense_account_id'],
                'amount' => $data['amount'],
                'includes_vat' => $data['includes_vat'] ?? false,
                'expense_date' => $data['expense_date'],
                'payment_method_id' => $data['payment_method_id'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'attachment_path' => $data['attachment'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $financeService = app(\Modules\CompanyVisas\Services\CompanyVisasFinanceService::class);
            $financeService->recordExpense($this->record, $expense);
        });
        $this->dispatch('refresh');
    }

    protected function updateStatus(array $data): void
    {
        $this->record->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $this->record->notes,
        ]);
        $this->dispatch('refresh');
    }

    protected function addContractCost(array $data): void
    {
        DB::transaction(function () use ($data) {
            $cost = CompanyVisaContractCost::create([
                'contract_id' => $this->record->id,
                'cost_per_worker' => $data['cost_per_worker'],
                'total_cost' => $data['total_cost'],
                'due_date' => $data['due_date'],
                'description' => $data['description'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $financeService = app(\Modules\CompanyVisas\Services\CompanyVisasFinanceService::class);
            $financeService->recordContractCost($this->record, $cost);
        });
        $this->dispatch('refresh');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
            Action::make('link_workers')
                ->label(tr('company_visas.actions.link_workers', [], null, 'dashboard') ?: 'ربط عمالة')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->form($this->getLinkWorkersForm())
                ->action(function (array $data) {
                    $this->linkWorkers($data);
                })
                ->visible(fn () => auth()->user()?->can('company_visas.link_workers') ?? false),

            Action::make('add_expense')
                ->label(tr('company_visas.actions.add_expense', [], null, 'dashboard') ?: 'إضافة مصروف')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->form($this->getAddExpenseForm())
                ->action(function (array $data) {
                    $this->addExpense($data);
                })
                ->visible(fn () => auth()->user()?->can('company_visas.add_expense') ?? false),

            Action::make('update_status')
                ->label(tr('company_visas.actions.update_status', [], null, 'dashboard') ?: 'تحديث الحالة')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->form($this->getUpdateStatusForm())
                ->action(function (array $data) {
                    $this->updateStatus($data);
                })
                ->visible(fn () => auth()->user()?->can('company_visas.update_status') ?? false),

            Action::make('contract_cost')
                ->label(tr('company_visas.actions.contract_cost', [], null, 'dashboard') ?: 'تكلفة الاستقدام')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->form($this->getContractCostForm())
                ->action(function (array $data) {
                    $this->addContractCost($data);
                })
                ->visible(fn () => auth()->user()?->can('company_visas.manage_cost') ?? false),
        ];
    }
}
