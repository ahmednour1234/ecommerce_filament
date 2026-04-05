<?php

namespace App\Filament\Pages\Finance;

use App\Exports\FinanceImportErrorExport;
use App\Exports\FinanceImportTemplateExport;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use App\Services\Finance\BranchTransactionImportService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportBranchTransactionsPage extends Page implements HasForms
{
    use InteractsWithForms;
    use FinanceModuleGate;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'قسم الحسابات';
    protected static ?string $navigationLabel = 'استيراد من Excel';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.finance.import-branch-transactions';

    public ?array $data = [];
    public array $lastImportedIds = [];

    public function mount(): void
    {
        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        $canViewAllBranches = (bool) ($user?->hasRole('super_admin') || $user?->can('finance.view_all_branches'));

        $saudiCountryId = Country::where('iso2', 'SA')->value('id');

        $this->form->fill([
            'branch_id' => !$canViewAllBranches && !empty($userBranches) ? $userBranches[0] : null,
            'kind' => null,
            'finance_type_id' => null,
            'currency_code' => 'SAR',
            'country_id' => $saudiCountryId,
            'payment_method' => null,
            'default_transaction_date' => now()->format('Y-m-d'),
            'global_notes' => null,
            'default_status' => 'approved',
            'allow_partial' => false,
            'on_duplicate' => 'skip',
            'excel_file' => null,
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        $canViewAllBranches = (bool) ($user?->hasRole('super_admin') || $user?->can('finance.view_all_branches'));

        return $form
            ->schema([
                Forms\Components\Section::make(tr('pages.finance.import.form_section', [], null, 'dashboard') ?: 'Import Settings')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('forms.branch_transactions.branch_id', [], null, 'dashboard') ?: 'Branch')
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options(function () use ($canViewAllBranches, $userBranches) {
                                $query = Branch::where('status', 'active');
                                if (!$canViewAllBranches && !empty($userBranches)) {
                                    $query->whereIn('id', $userBranches);
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('kind')
                            ->label(tr('forms.finance_types.kind', [], null, 'dashboard') ?: 'Kind')
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options([
                                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                            ])
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('finance_type_id', null)),

                        Forms\Components\Select::make('finance_type_id')
                            ->label(tr('forms.branch_transactions.finance_type_id', [], null, 'dashboard') ?: 'Finance Type')
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options(function (callable $get) {
                                $kind = $get('kind');
                                if (!$kind) {
                                    return [];
                                }
                                return FinanceType::where('kind', $kind)
                                    ->where('is_active', true)
                                    ->get()
                                    ->pluck('name_text', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->visible(fn (callable $get) => !empty($get('kind'))),

                        Forms\Components\Select::make('currency_code')
                            ->label(tr('forms.branch_transactions.currency_id', [], null, 'dashboard') ?: 'Currency')
                            ->options(['SAR' => 'SAR - ريال سعودي'])
                            ->default('SAR')
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('forms.branch_transactions.country_id', [], null, 'dashboard') ?: 'Country')
                            ->options(function () {
                                $saudi = Country::where('iso2', 'SA')->first();
                                return $saudi ? [$saudi->id => $saudi->name_text ?? ($saudi->name['ar'] ?? 'المملكة العربية السعودية')] : [];
                            })
                            ->default(fn () => Country::where('iso2', 'SA')->value('id'))
                            ->disabled()
                            ->dehydrated()
                            ->nullable(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label(tr('forms.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\DatePicker::make('default_transaction_date')
                            ->label(tr('pages.finance.import.default_transaction_date', [], null, 'dashboard') ?: 'Default Transaction Date')
                            ->default(now())
                            ->nullable(),

                        Forms\Components\Textarea::make('global_notes')
                            ->label(tr('pages.finance.import.global_notes', [], null, 'dashboard') ?: 'Global Notes')
                            ->rows(3)
                            ->nullable(),

                        Forms\Components\Select::make('default_status')
                            ->label(tr('pages.finance.import.default_status', [], null, 'dashboard') ?: 'Default Status')
                            ->options([
                                'pending' => tr('forms.status.pending', [], null, 'dashboard') ?: 'Pending',
                                'approved' => tr('forms.status.approved', [], null, 'dashboard') ?: 'Approved',
                            ])
                            ->default('approved')
                            ->required()
                            ->reactive(),

                        Forms\Components\Toggle::make('allow_partial')
                            ->label(tr('pages.finance.import.allow_partial', [], null, 'dashboard') ?: 'Allow Partial Import')
                            ->helperText(tr('pages.finance.import.allow_partial_helper', [], null, 'dashboard') ?: 'Import valid rows even if some rows have errors')
                            ->default(false),

                        Forms\Components\Select::make('on_duplicate')
                            ->label(tr('pages.finance.import.on_duplicate', [], null, 'dashboard') ?: 'On Duplicate')
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options([
                                'skip' => tr('pages.finance.import.duplicate_skip', [], null, 'dashboard') ?: 'Skip',
                                'update' => tr('pages.finance.import.duplicate_update', [], null, 'dashboard') ?: 'Update',
                            ])
                            ->default('skip')
                            ->required(),

                        Forms\Components\FileUpload::make('excel_file')
                            ->label(tr('pages.finance.import.excel_file', [], null, 'dashboard') ?: 'Excel File')
                            ->placeholder(tr('forms.common.no_file_chosen', [], null, 'dashboard') ?: tr('forms.branch_transactions.no_file_chosen', [], null, 'dashboard') ?: 'No file chosen')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->disk('local')
                            ->directory('imports/finance')
                            ->required()
                            ->helperText(tr('pages.finance.import.excel_file_helper', [], null, 'dashboard') ?: 'Upload .xlsx or .xls file'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        $canViewAllBranches = (bool) ($user?->hasRole('super_admin') || $user?->can('finance.view_all_branches'));

        return [
            \Filament\Actions\Action::make('download_template')
                ->label(tr('pages.finance.import.download_template', [], null, 'dashboard') ?: 'تحميل قالب Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading(tr('pages.finance.import.download_template', [], null, 'dashboard') ?: 'تحميل قالب Excel')
                ->modalSubmitActionLabel(tr('actions.download', [], null, 'dashboard') ?: 'تحميل')
                ->form([
                    Forms\Components\Select::make('branch_id')
                        ->label(tr('forms.branch_transactions.branch_id', [], null, 'dashboard') ?: 'الفرع')
                        ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: 'اختر')
                        ->options(function () use ($canViewAllBranches, $userBranches) {
                            $query = Branch::where('status', 'active');
                            if (!$canViewAllBranches && !empty($userBranches)) {
                                $query->whereIn('id', $userBranches);
                            }
                            return $query->pluck('name', 'id');
                        })
                        ->searchable()
                        ->nullable(),

                    Forms\Components\Select::make('kind')
                        ->label(tr('forms.finance_types.kind', [], null, 'dashboard') ?: 'النوع')
                        ->options([
                            'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'إيراد',
                            'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'مصروف',
                        ])
                        ->required()
                        ->native(false)
                        ->default('expense'),
                ])
                ->fillForm(function () {
                    try {
                        $data = $this->form->getRawState();
                        return [
                            'kind' => in_array($data['kind'] ?? '', ['income', 'expense']) ? $data['kind'] : 'expense',
                            'branch_id' => $data['branch_id'] ?? null,
                        ];
                    } catch (\Exception $e) {
                        return ['kind' => 'expense', 'branch_id' => null];
                    }
                })
                ->action(function (array $data) {
                    $kind = in_array($data['kind'] ?? '', ['income', 'expense']) ? $data['kind'] : 'expense';
                    $url = route('finance.import.template', [
                        'kind' => $kind,
                        'branch_id' => $data['branch_id'] ?? null,
                    ]);
                    return redirect($url);
                }),
        ];
    }

    public function import()
    {
        $data = $this->form->getState();

        if (empty($data['excel_file'])) {
            Notification::make()
                ->danger()
                ->title(tr('pages.finance.import.no_file', [], null, 'dashboard') ?: 'Please upload an Excel file')
                ->send();
            return;
        }

        $filePath = Storage::disk('local')->path($data['excel_file']);

        $currency = Currency::where('code', $data['currency_code'])->first();
        if (!$currency) {
            Notification::make()
                ->danger()
                ->title(tr('pages.finance.import.invalid_currency', [], null, 'dashboard') ?: 'Invalid currency code')
                ->send();
            return;
        }

        $config = [
            'branch_id' => $data['branch_id'],
            'finance_type_id' => $data['finance_type_id'],
            'currency_id' => $currency->id,
            'country_id' => $data['country_id'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'default_transaction_date' => $data['default_transaction_date'] ?? null,
            'global_notes' => $data['global_notes'] ?? null,
            'default_status' => $data['default_status'] ?? 'approved',
            'allow_partial' => $data['allow_partial'] ?? false,
            'on_duplicate' => $data['on_duplicate'] ?? 'skip',
        ];

        $service = app(BranchTransactionImportService::class);
        $result = $service->import($filePath, $config);

        if ($result->failed > 0 && !$config['allow_partial']) {
            $errorExport = new FinanceImportErrorExport($result->errors);
            $errorFilename = 'finance-import-errors-' . date('Y-m-d_His') . '.xlsx';

            Notification::make()
                ->danger()
                ->title(tr('pages.finance.import.import_failed', [], null, 'dashboard') ?: 'Import Failed')
                ->body(tr('pages.finance.import.errors_found', ['count' => $result->failed], null, 'dashboard') ?: "Found {$result->failed} errors. Downloading error report...")
                ->send();

            return Excel::download($errorExport, $errorFilename);
        }

        $this->lastImportedIds = array_merge($result->importedIds, $result->updatedIds);

        $message = tr('pages.finance.import.success', [
            'imported' => $result->imported,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'failed' => $result->failed,
        ], null, 'dashboard') ?: "Imported: {$result->imported}, Updated: {$result->updated}, Skipped: {$result->skipped}, Failed: {$result->failed}";

        if ($result->failed > 0) {
            $errorExport = new FinanceImportErrorExport($result->errors);
            $errorFilename = 'finance-import-errors-' . date('Y-m-d_His') . '.xlsx';

            Notification::make()
                ->warning()
                ->title(tr('pages.finance.import.partial_success', [], null, 'dashboard') ?: 'Partial Import Success')
                ->body($message . ' ' . (tr('pages.finance.import.downloading_errors', [], null, 'dashboard') ?: 'Downloading error report...'))
                ->actions($this->getApproveAction($this->lastImportedIds))
                ->send();

            return Excel::download($errorExport, $errorFilename);
        } else {
            Notification::make()
                ->success()
                ->title(tr('pages.finance.import.import_success', [], null, 'dashboard') ?: 'Import Successful')
                ->body($message)
                ->actions($this->getApproveAction($this->lastImportedIds))
                ->send();
        }

        $this->form->fill([
            'excel_file' => null,
        ]);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin')
            || $user?->can('finance.transactions.import')
            || $user?->can('finance.create_transactions'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getTitle(): string
    {
        return tr('pages.finance.import.title', [], null, 'dashboard') ?: 'استيراد من Excel';
    }

    public function getHeading(): string
    {
        return tr('pages.finance.import.title', [], null, 'dashboard') ?: 'استيراد من Excel';
    }

    protected function getApproveAction(array $transactionIds): array
    {
        if (empty($transactionIds)) {
            return [];
        }

        $user = auth()->user();
        $canApprove = $user?->hasRole('super_admin') || $user?->can('finance.approve_transactions') ?? false;

        if (!$canApprove) {
            return [];
        }

        return [
            \Filament\Notifications\Actions\Action::make('approve_all')
                ->label(tr('pages.finance.import.approve_all', [], null, 'dashboard') ?: 'Approve All Imported')
                ->action(function () use ($transactionIds) {
                    $this->approveAllImported($transactionIds);
                }),
        ];
    }

    public function approveAllImported(array $transactionIds): void
    {
        if (empty($transactionIds)) {
            return;
        }

        $user = auth()->user();
        $canApprove = $user?->hasRole('super_admin') || $user?->can('finance.approve_transactions') ?? false;

        if (!$canApprove) {
            Notification::make()
                ->danger()
                ->title(tr('pages.finance.import.no_permission', [], null, 'dashboard') ?: 'Permission Denied')
                ->body(tr('pages.finance.import.approve_permission_required', [], null, 'dashboard') ?: 'You do not have permission to approve transactions.')
                ->send();
            return;
        }

        $approved = 0;
        $failed = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($transactionIds, &$approved, &$failed) {
            foreach ($transactionIds as $id) {
                $transaction = BranchTransaction::find($id);
                if ($transaction && $transaction->status === 'pending') {
                    try {
                        $transaction->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        $approved++;
                    } catch (\Exception $e) {
                        $failed++;
                    }
                }
            }
        });

        if ($approved > 0) {
            Notification::make()
                ->success()
                ->title(tr('pages.finance.import.approved_success', [], null, 'dashboard') ?: 'Approval Successful')
                ->body(tr('pages.finance.import.approved_count', ['count' => $approved], null, 'dashboard') ?: "Approved {$approved} transaction(s).")
                ->send();
        }

        if ($failed > 0) {
            Notification::make()
                ->warning()
                ->title(tr('pages.finance.import.approval_partial', [], null, 'dashboard') ?: 'Partial Approval')
                ->body(tr('pages.finance.import.failed_count', ['count' => $failed], null, 'dashboard') ?: "Failed to approve {$failed} transaction(s).")
                ->send();
        }

        $this->lastImportedIds = [];
    }
}
