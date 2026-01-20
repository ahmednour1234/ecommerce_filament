<?php

namespace App\Filament\Pages\Finance;

use App\Exports\FinanceImportErrorExport;
use App\Exports\FinanceImportTemplateExport;
use App\Filament\Concerns\FinanceModuleGate;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use App\Services\Finance\BranchTransactionImportService;
use App\Services\MainCore\CurrencyService;
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
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.finance.import-branch-transactions';

    protected static ?string $navigationTranslationKey = 'sidebar.finance.import';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $userBranches = $user?->branches()->pluck('branches.id')->toArray() ?? [];
        $canViewAllBranches = (bool) ($user?->hasRole('super_admin') || $user?->can('finance.view_all_branches'));

        $defaultCurrency = app(CurrencyService::class)->defaultCurrency();

        $this->form->fill([
            'branch_id' => !$canViewAllBranches && !empty($userBranches) ? $userBranches[0] : null,
            'kind' => null,
            'finance_type_id' => null,
            'currency_code' => $defaultCurrency?->code,
            'country_id' => null,
            'payment_method' => null,
            'default_transaction_date' => now()->format('Y-m-d'),
            'global_notes' => null,
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
                            ->options(function () {
                                return Branch::where('status', 'active')
                                    ->get()
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn () => !$canViewAllBranches)
                            ->default(fn () => !$canViewAllBranches && !empty($userBranches) ? $userBranches[0] : null)
                            ->visible(fn () => $canViewAllBranches || !empty($userBranches))
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
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options(function () {
                                return Currency::where('is_active', true)
                                    ->get()
                                    ->pluck('code', 'code');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('country_id')
                            ->label(tr('forms.branch_transactions.country_id', [], null, 'dashboard') ?: 'Country')
                            ->placeholder(tr('forms.common.select_placeholder', [], null, 'dashboard') ?: tr('forms.branch_transactions.select', [], null, 'dashboard') ?: 'Select')
                            ->options(function () {
                                return Country::where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($country) {
                                        return [$country->id => $country->name_text ?? $country->name['en'] ?? ''];
                                    });
                            })
                            ->searchable()
                            ->preload()
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
        return [
            \Filament\Actions\Action::make('download_template')
                ->label(tr('pages.finance.import.download_template', [], null, 'dashboard') ?: 'تحميل قالب Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $data = $this->form->getState();
                    $branchId = $data['branch_id'] ?? null;
                    $kind = $data['kind'] ?? null;
                    $financeTypeId = $data['finance_type_id'] ?? null;

                    if (!$branchId) {
                        Notification::make()
                            ->warning()
                            ->title(tr('pages.finance.import.select_branch_first', [], null, 'dashboard') ?: 'Please select Branch first')
                            ->send();
                        return;
                    }

                    if (!$kind) {
                        Notification::make()
                            ->warning()
                            ->title(tr('pages.finance.import.select_kind_first', [], null, 'dashboard') ?: 'Please select Kind first')
                            ->send();
                        return;
                    }

                    if (!$financeTypeId) {
                        Notification::make()
                            ->warning()
                            ->title(tr('pages.finance.import.select_type_first', [], null, 'dashboard') ?: 'Please select Finance Type first')
                            ->send();
                        return;
                    }

                    $export = new FinanceImportTemplateExport($kind);
                    $filename = 'finance-import-' . ($kind === 'expense' ? 'expenses' : 'income') . '-template.xlsx';
                    return Excel::download($export, $filename);
                })
                ->requiresConfirmation(false),
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
                ->send();

            return Excel::download($errorExport, $errorFilename);
        } else {
            Notification::make()
                ->success()
                ->title(tr('pages.finance.import.import_success', [], null, 'dashboard') ?: 'Import Successful')
                ->body($message)
                ->send();
        }

        $this->form->fill([
            'excel_file' => null,
        ]);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->hasRole('super_admin') || ($user?->can('finance.transactions.import') ?? false));
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
}
