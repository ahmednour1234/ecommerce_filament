<?php

namespace App\Filament\Pages\Accounting;

use App\Models\Accounting\Account;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Response;

class AccountsTreePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.accounting.accounts-tree';

    public $accounts = [];
    public $selectedAccountType = 'all';
    public $expandedAccounts = [];
    public $selectedAccountId = null;
    public $searchTerm = '';
    
    // Form properties for edit/create
    public $showModal = false;
    public $isEditing = false;
    public $formData = [
        'code' => '',
        'name' => '',
        'type' => '',
        'parent_id' => null,
        'level' => 1,
        'is_active' => true,
        'allow_manual_entry' => true,
        'notes' => '',
    ];

    public function mount(): void
    {
        $this->loadAccounts();
        // Expand all accounts by default
        $this->expandAll();
    }

    public function updatedSelectedAccountType(): void
    {
        $this->loadAccounts();
        // Reset and expand all accounts after filtering
        $this->expandAll();
        $this->selectedAccountId = null;
    }

    public function updatedSearchTerm(): void
    {
        $this->loadAccounts();
        $this->expandAll();
    }

    protected function loadAccounts(): void
    {
        $query = Account::query();

        if ($this->selectedAccountType !== 'all') {
            $query->where('type', $this->selectedAccountType);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $allAccounts = $query->orderBy('code')->get();
        
        // Build tree structure
        $this->accounts = $this->buildTree($allAccounts);
    }

    public function selectAccount($accountId): void
    {
        $this->selectedAccountId = $accountId;
    }

    public function getSelectedAccountProperty()
    {
        if (!$this->selectedAccountId) {
            return null;
        }
        
        return Account::with('parent')->find($this->selectedAccountId);
    }

    protected function buildTree($accounts, $parentId = null): array
    {
        $tree = [];

        foreach ($accounts as $account) {
            if ($account->parent_id === $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                $tree[] = [
                    'account' => $account,
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    public function toggleAccount($accountId): void
    {
        if (in_array($accountId, $this->expandedAccounts)) {
            $this->expandedAccounts = array_values(array_diff($this->expandedAccounts, [$accountId]));
        } else {
            $this->expandedAccounts[] = $accountId;
        }
    }

    public function isExpanded($accountId): bool
    {
        return in_array($accountId, $this->expandedAccounts);
    }

    protected function expandAll(): void
    {
        $this->expandedAccounts = [];
        $this->collectAccountIds($this->accounts);
    }

    protected function collectAccountIds($accounts): void
    {
        foreach ($accounts as $item) {
            $account = $item['account'];
            $children = $item['children'] ?? [];
            
            if (count($children) > 0) {
                $this->expandedAccounts[] = $account->id;
                $this->collectAccountIds($children);
            }
        }
    }

    public function exportToExcel()
    {
        $accounts = Account::orderBy('code')->get();
        
        $filename = 'accounts_export_' . date('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Add BOM for UTF-8
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($file, [
            trans_dash('pages.accounts_tree.export.headers.code'),
            trans_dash('pages.accounts_tree.export.headers.name'),
            trans_dash('pages.accounts_tree.export.headers.type'),
            trans_dash('pages.accounts_tree.export.headers.parent_account'),
            trans_dash('pages.accounts_tree.export.headers.level'),
            trans_dash('pages.accounts_tree.export.headers.active'),
            trans_dash('pages.accounts_tree.export.headers.allow_manual_entry'),
            trans_dash('pages.accounts_tree.export.headers.notes'),
        ]);

        // Data
        foreach ($accounts as $account) {
            $typeTranslations = [
                'asset' => trans_dash('pages.accounts_tree.account_type.asset'),
                'liability' => trans_dash('pages.accounts_tree.account_type.liability'),
                'equity' => trans_dash('pages.accounts_tree.account_type.equity'),
                'revenue' => trans_dash('pages.accounts_tree.account_type.revenue'),
                'expense' => trans_dash('pages.accounts_tree.account_type.expense'),
            ];
            
            fputcsv($file, [
                $account->code,
                $account->name,
                $typeTranslations[$account->type] ?? ucfirst($account->type),
                $account->parent ? $account->parent->name : '',
                $account->level,
                $account->is_active ? trans_dash('pages.accounts_tree.export.yes') : trans_dash('pages.accounts_tree.export.no'),
                $account->allow_manual_entry ? trans_dash('pages.accounts_tree.export.yes') : trans_dash('pages.accounts_tree.export.no'),
                $account->notes ?? '',
            ]);
        }

        fclose($file);
        
        return Response::download($filepath, $filename)->deleteFileAfterSend(true);
    }

    public function resetFilters(): void
    {
        $this->selectedAccountType = 'all';
        $this->searchTerm = '';
        $this->selectedAccountId = null;
        $this->loadAccounts();
        $this->expandAll();
    }

    public function deleteAccount($accountId): void
    {
        $account = Account::find($accountId);
        
        if ($account && auth()->user()?->can('accounts.delete')) {
            // Check if account has children
            if ($account->children()->count() > 0) {
                session()->flash('error', 'Cannot delete account with child accounts. Please delete child accounts first.');
                return;
            }
            
            // Check if account has any journal entries or transactions
            if ($account->hasTransactions()) {
                $transactionCount = $account->transaction_count;
                session()->flash('error', "Cannot delete account. This account has {$transactionCount} transaction(s) or journal entry line(s). Please remove all transactions first.");
                return;
            }
            
            $account->delete();
            $this->selectedAccountId = null;
            $this->loadAccounts();
            $this->expandAll();
            session()->flash('success', 'Account deleted successfully.');
        }
    }

    public function openCreateModal($parentId = null): void
    {
        $this->isEditing = false;
        $this->showModal = true;
        $this->formData = [
            'code' => '',
            'name' => '',
            'type' => $this->selectedAccountType !== 'all' ? $this->selectedAccountType : 'asset',
            'parent_id' => $parentId,
            'level' => $parentId ? (Account::find($parentId)?->level + 1 ?? 1) : 1,
            'is_active' => true,
            'allow_manual_entry' => true,
            'notes' => '',
        ];
    }

    public function openEditModal($accountId): void
    {
        $account = Account::find($accountId);
        
        if ($account) {
            $this->isEditing = true;
            $this->showModal = true;
            $this->formData = [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'parent_id' => $account->parent_id,
                'level' => $account->level,
                'is_active' => $account->is_active,
                'allow_manual_entry' => $account->allow_manual_entry,
                'notes' => $account->notes ?? '',
            ];
            $this->selectedAccountId = $accountId;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->formData = [
            'code' => '',
            'name' => '',
            'type' => '',
            'parent_id' => null,
            'level' => 1,
            'is_active' => true,
            'allow_manual_entry' => true,
            'notes' => '',
        ];
    }

    public function updatedFormDataParentId(): void
    {
        if ($this->formData['parent_id']) {
            $parent = Account::find($this->formData['parent_id']);
            if ($parent) {
                $this->formData['level'] = $parent->level + 1;
            }
        } else {
            $this->formData['level'] = 1;
        }
    }

    public function updateParentLevel(): void
    {
        $this->updatedFormDataParentId();
    }

    public function saveAccount(): void
    {
        $this->validate([
            'formData.code' => 'required|max:50|unique:accounts,code' . ($this->isEditing ? ',' . $this->selectedAccountId : ''),
            'formData.name' => 'required|max:255',
            'formData.type' => 'required|in:asset,liability,equity,revenue,expense',
            'formData.parent_id' => 'nullable|exists:accounts,id',
            'formData.level' => 'required|integer|min:1',
            'formData.is_active' => 'boolean',
            'formData.allow_manual_entry' => 'boolean',
        ]);

        if ($this->isEditing) {
            $account = Account::find($this->selectedAccountId);
            if ($account && auth()->user()?->can('accounts.update')) {
                $account->update($this->formData);
                session()->flash('success', 'Account updated successfully.');
            }
        } else {
            if (auth()->user()?->can('accounts.create')) {
                Account::create($this->formData);
                session()->flash('success', 'Account created successfully.');
            }
        }

        $this->loadAccounts();
        $this->expandAll();
        $this->closeModal();
    }

    public function getParentAccountsProperty()
    {
        if (empty($this->formData['type'])) {
            return [];
        }
        
        $query = Account::where('type', $this->formData['type'])
            ->orderBy('code');
            
        if ($this->isEditing && $this->selectedAccountId) {
            $query->where('id', '!=', $this->selectedAccountId);
        }
        
        return $query->get()->map(fn($account) => ['id' => $account->id, 'name' => $account->code . ' - ' . $account->name]);
    }

    protected function getViewData(): array
    {
        return [
            'accounts' => $this->accounts,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('accounts.view_any') ?? false;
    }
}
